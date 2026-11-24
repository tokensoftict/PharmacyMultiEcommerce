<?php

namespace Tests\Feature;

use App\Classes\ApplicationEnvironment;
use App\Enums\Permission\Settings as SettingsPermissions;
use App\Events\Opay\OpayWebhookReceived;
use App\Livewire\Backend\Admin\Settings\OpaySettingsComponent;
use App\Models\OpaySetting;
use App\Models\OpayWebhookLog;
use App\Services\Opay\OpayClient;
use App\Services\Opay\OpayRsaCrypto;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Tests\TestCase;

class OpayIntegrationTest extends TestCase
{
    private OpayRsaCrypto $crypto;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate', [
            '--path' => 'database/migrations/2026_11_24_120000_create_opay_settings_table.php',
        ]);

        config(['cache.default' => 'array']);
        $this->crypto = app(OpayRsaCrypto::class);
        ApplicationEnvironment::$storePrefix = config('app.SUPERMARKET_ADMIN_ROUTE_PREFIX');
    }

    public function test_opay_settings_route_and_permission_exist_under_settings(): void
    {
        $supermarketRoute = config('app.SUPERMARKET_ADMIN_ROUTE_PREFIX') . 'backend.admin.settings.opay_settings';
        $wholesalesRoute = config('app.WHOLESALES_ADMIN_ROUTE_PREFIX') . 'backend.admin.settings.opay_settings';

        $this->assertTrue(Route::has($supermarketRoute), "Expected route {$supermarketRoute} to be registered.");
        $this->assertTrue(Route::has($wholesalesRoute), "Expected route {$wholesalesRoute} to be registered.");
        $this->assertTrue(Route::has('api.opay.webhook'), 'Expected route api.opay.webhook to be registered.');

        $permissionNames = array_column(SettingsPermissions::PERMISSION, 'name');
        $this->assertContains('backend.admin.settings.opay_settings', $permissionNames);
        $this->assertContains('backend.admin.settings.opay_settings.update', $permissionNames);

        $menuEntry = collect(SettingsPermissions::PERMISSION)
            ->firstWhere('name', 'backend.admin.settings.opay_settings');
        $this->assertSame('1', $menuEntry['visibility']);
        $this->assertSame('1', $menuEntry['module_id']);
    }

    public function test_admin_can_generate_rsa_keys_and_save_opay_dashboard_settings(): void
    {
        $opayKeyPair = $this->crypto->generateKeyPair(1024);

        Livewire::test(OpaySettingsComponent::class)
            ->call('generateKeyPair')
            ->assertHasNoErrors()
            ->set('is_enabled', true)
            ->set('opay_public_key', $opayKeyPair['public_key_raw'])
            ->set('client_auth_key', '9a2ca7297afd493a84d7a538a04e210m')
            ->set('ip_whitelisting', '11.11.11.11, 22.22.22.22')
            ->set('webhook_url', 'https://generaldrugcentre.com/api/opay/webhook')
            ->set('head_merchant_id', '256622092286390')
            ->set('merchant_id', '256622092286391')
            ->call('save')
            ->assertHasNoErrors();

        $saved = OpaySetting::current();
        $this->assertTrue($saved->exists);
        $this->assertTrue($saved->is_enabled);
        $this->assertTrue($saved->isConfigured());
        $this->assertSame('9a2ca7297afd493a84d7a538a04e210m', $saved->client_auth_key);
        $this->assertSame('11.11.11.11,22.22.22.22', $saved->ip_whitelisting);
        $this->assertSame('https://generaldrugcentre.com/api/opay/webhook', $saved->webhook_url);
        $this->assertSame(['11.11.11.11', '22.22.22.22'], $saved->getIpWhitelistArray());

        // Ensure sensitive fields are encrypted in the raw database column (not plaintext)
        $rawRow = \DB::table('opay_settings')->first();
        $this->assertNotSame('9a2ca7297afd493a84d7a538a04e210m', $rawRow->client_auth_key);
        $this->assertStringNotContainsString('BEGIN PRIVATE KEY', (string) $rawRow->merchant_private_key);

        // Editing another field while leaving client_auth_key blank preserves the saved Client Auth Key
        Livewire::test(OpaySettingsComponent::class)
            ->set('ip_whitelisting', '33.33.33.33')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('9a2ca7297afd493a84d7a538a04e210m', OpaySetting::current()->client_auth_key);
        $this->assertSame('33.33.33.33', OpaySetting::current()->ip_whitelisting);
    }

    public function test_admin_settings_rejects_invalid_ip_and_invalid_public_key(): void
    {
        Livewire::test(OpaySettingsComponent::class)
            ->set('opay_public_key', 'not-a-valid-rsa-key')
            ->set('ip_whitelisting', '11.11.11.11,999.999.999.999')
            ->set('webhook_url', 'not-a-url')
            ->call('save')
            ->assertHasErrors(['opay_public_key', 'ip_whitelisting', 'webhook_url']);
    }

    public function test_opay_client_sends_encrypted_signed_request_and_decrypts_verified_response(): void
    {
        $merchantKeys = $this->crypto->generateKeyPair(1024);
        $opayKeys = $this->crypto->generateKeyPair(1024);

        OpaySetting::create([
            'is_enabled' => true,
            'environment' => 'live',
            'base_url' => 'https://payapi.opayweb.com',
            'api_version' => 'V1.0.1',
            'head_merchant_id' => '256621072768092',
            'merchant_id' => '256621072768092',
            'client_auth_key' => '9a2ca7297afd493a84d7a538a04e210m',
            'opay_public_key' => $opayKeys['public_key_pem'],
            'merchant_public_key' => $merchantKeys['public_key_pem'],
            'merchant_private_key' => $merchantKeys['private_key_pem'],
            'key_bits' => 1024,
        ]);

        Http::fake(function (Request $request) use ($merchantKeys, $opayKeys) {
            $this->assertSame('https://payapi.opayweb.com/openApi/merchant/queryMerchantBalance', $request->url());
            $this->assertSame('9a2ca7297afd493a84d7a538a04e210m', $request->header('clientAuthKey')[0] ?? null);
            $this->assertSame('V1.0.1', $request->header('version')[0] ?? null);
            $this->assertSame('JSON', $request->header('bodyFormat')[0] ?? null);
            $timestamp = $request->header('timestamp')[0] ?? '';
            $this->assertNotEmpty($timestamp);

            $body = $request->data();
            $this->assertTrue(
                $this->crypto->verify($body['paramContent'] . $timestamp, $body['sign'], $merchantKeys['public_key_pem']),
                'Outgoing request signature failed verification against Merchant Public Key.'
            );

            $decryptedReq = json_decode(
                $this->crypto->decryptByPrivateKey($body['paramContent'], $opayKeys['private_key_pem']),
                true
            );
            $this->assertSame(['opayMerchantId' => '256621072768092'], $decryptedReq);

            $responsePayload = [
                'opayMerchantId' => '256621072768092',
                'opayMerchantName' => 'PS General Drug Centre',
                'usableAmount' => '2500000',
                'currency' => 'NGN',
                'opayAccountNo' => '6151576968',
                'queryTime' => '2026-11-24 12:30:00',
            ];
            $encryptedData = $this->crypto->encryptByPublicKey(
                $this->crypto->canonicalJson($responsePayload),
                $merchantKeys['public_key_pem'],
            );
            $envelope = [
                'code' => '00000',
                'message' => 'SUCCESSFUL',
                'timestamp' => '1732446600000',
                'data' => $encryptedData,
            ];
            $envelope['sign'] = $this->crypto->sign(
                $this->crypto->buildResponseSignatureContent($envelope),
                $opayKeys['private_key_pem'],
            );

            return Http::response($envelope, 200);
        });

        $client = app(OpayClient::class);
        $result = $client->queryMerchantBalance();

        $this->assertTrue($result['verified']);
        $this->assertSame('00000', $result['code']);
        $this->assertSame('PS General Drug Centre', $result['data']['opayMerchantName']);
        $this->assertSame('2500000', $result['data']['usableAmount']);

        // Also test the Livewire "Test Live Connection" button
        Livewire::test(OpaySettingsComponent::class)
            ->call('testConnection')
            ->assertSet('diagnosticResult.ok', true)
            ->assertSet('last_test_status', 'success');
    }

    public function test_webhook_endpoint_accepts_plaintext_general_payment_webhook(): void
    {
        Event::fake([OpayWebhookReceived::class]);

        $response = $this->postJson('/api/opay/webhook', [
            'data' => [
                'outOrderNo' => 'ORD-1001',
                'orderNo' => '20261124741380171865833472',
                'payNo' => 'PAY-9991',
                'merchantId' => '256622092286391',
                'status' => 'SUCCESS',
                'amount' => '300.00',
                'currency' => 'NGN',
            ],
        ], [
            'X-Opay-Tranid' => 'TRAN-123456',
            'merchantId' => '256622092286391',
        ]);

        $response->assertOk()
            ->assertExactJson([
                'code' => '00000',
                'message' => 'SUCCESSFUL',
            ]);

        $log = OpayWebhookLog::firstOrFail();
        $this->assertSame('TRAN-123456', $log->transaction_id);
        $this->assertSame('ORD-1001', $log->out_order_no);
        $this->assertSame('SUCCESS', $log->status);
        $this->assertSame('300.00', $log->amount);

        Event::assertDispatched(OpayWebhookReceived::class, function (OpayWebhookReceived $event): bool {
            return ($event->data['outOrderNo'] ?? null) === 'ORD-1001'
                && ($event->headers['x-opay-tranid'] ?? null) === 'TRAN-123456';
        });
    }

    public function test_webhook_endpoint_verifies_and_decrypts_signed_encrypted_webhook_and_rejects_bad_signature(): void
    {
        Event::fake([OpayWebhookReceived::class]);

        $merchantKeys = $this->crypto->generateKeyPair(1024);
        $opayKeys = $this->crypto->generateKeyPair(1024);

        OpaySetting::create([
            'is_enabled' => true,
            'base_url' => 'https://payapi.opayweb.com',
            'api_version' => 'V1.0.1',
            'client_auth_key' => '9a2ca7297afd493a84d7a538a04e210m',
            'opay_public_key' => $opayKeys['public_key_pem'],
            'merchant_public_key' => $merchantKeys['public_key_pem'],
            'merchant_private_key' => $merchantKeys['private_key_pem'],
        ]);

        $payload = [
            'outOrderNo' => 'ORD-ENC-2002',
            'orderNo' => '20261124000000002002',
            'status' => 'SUCCESS',
            'amount' => '1500.00',
            'currency' => 'NGN',
        ];
        $encData = $this->crypto->encryptByPublicKey(
            $this->crypto->canonicalJson($payload),
            $merchantKeys['public_key_pem'],
        );
        $signedBody = [
            'code' => '00000',
            'message' => 'SUCCESSFUL',
            'timestamp' => '1732446999000',
            'data' => $encData,
        ];
        $signedBody['sign'] = $this->crypto->sign(
            $this->crypto->buildResponseSignatureContent($signedBody),
            $opayKeys['private_key_pem'],
        );

        // Valid signed webhook succeeds
        $this->postJson('/api/opay/webhook', $signedBody, ['X-Opay-Tranid' => 'TRAN-ENC-1'])
            ->assertOk()
            ->assertExactJson(['code' => '00000', 'message' => 'SUCCESSFUL']);

        $log = OpayWebhookLog::where('out_order_no', 'ORD-ENC-2002')->firstOrFail();
        $this->assertTrue($log->signature_verified);
        $this->assertSame('1500.00', $log->amount);

        // Tampered signature rejected with 401
        $signedBody['sign'] = base64_encode('forged-signature');
        $this->postJson('/api/opay/webhook', $signedBody)
            ->assertStatus(401)
            ->assertJson(['code' => '00004']);
    }

    public function test_admin_can_import_custom_key_pair_and_run_crypto_self_test(): void
    {
        $merchantPair = $this->crypto->generateKeyPair(1024);
        $opayPair = $this->crypto->generateKeyPair(1024);

        Livewire::test(OpaySettingsComponent::class)
            ->set('import_merchant_public_key', $merchantPair['public_key_raw'])
            ->set('import_merchant_private_key', $merchantPair['private_key_pem'])
            ->call('importKeyPair')
            ->assertHasNoErrors()
            ->set('opay_public_key', $opayPair['public_key_raw'])
            ->call('save')
            ->assertHasNoErrors()
            ->call('verifyCrypto')
            ->assertSet('diagnosticResult.ok', true);
    }

    public function test_permission_migration_idempotently_seeds_opay_settings_under_settings_module(): void
    {
        $this->artisan('migrate', [
            '--path' => 'database/migrations/2023_02_17_141330_create_modules_table.php',
        ]);
        $this->artisan('migrate', [
            '--path' => 'database/migrations/2024_01_20_210918_create_permission_tables.php',
        ]);
        \Illuminate\Support\Facades\Schema::create('apps', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->timestamps();
        });
        \DB::table('apps')->insert([
            ['id' => 2, 'name' => 'Supermarket Admin', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Wholesales Admin', 'created_at' => now(), 'updated_at' => now()],
        ]);
        $this->artisan('migrate', [
            '--path' => 'database/migrations/2025_03_08_061526_create_permission_app_mappers_table.php',
        ]);

        $moduleId = \DB::table('modules')->insertGetId([
            'name' => 'Settings',
            'label' => 'Settings',
            'status' => 1,
            'visibility' => 1,
            'order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->artisan('migrate', [
            '--path' => 'database/migrations/2026_11_24_120001_seed_opay_settings_permission.php',
        ]);

        $viewPerm = \DB::table('permissions')->where('name', 'backend.admin.settings.opay_settings')->first();
        $updatePerm = \DB::table('permissions')->where('name', 'backend.admin.settings.opay_settings.update')->first();

        $this->assertNotNull($viewPerm);
        $this->assertSame($moduleId, (int) $viewPerm->module_id);
        $this->assertSame('1', (string) $viewPerm->visibility);
        $this->assertSame('OPay Settings', $viewPerm->label);

        $this->assertNotNull($updatePerm);
        $this->assertSame('0', (string) $updatePerm->visibility);

        $mappedApps = \DB::table('permission_app_mappers')
            ->where('permission_id', $viewPerm->id)
            ->pluck('app_id')
            ->map(fn ($id) => (int) $id)
            ->all();
        $this->assertSame([2, 3], $mappedApps);
    }
}
