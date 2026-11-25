@section('pageHeaderTitle')
    OPay Authentication Settings
@endsection

@push('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route(\App\Classes\ApplicationEnvironment::$storePrefix.'admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">OPay Settings</li>
@endpush

@php
    $canEditOpay = !auth()->check()
        || auth()->user()->can('backend.admin.settings.opay_settings.update')
        || auth()->user()->can('backend.admin.settings.system_settings.update');
@endphp

<div>
    {{-- ── Integration Status Banner ──────────────────────────────────────── --}}
    <div class="card shadow-none border mb-4">
        <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h4 class="mb-0">OPay Offline API (POS &amp; Others) Authentication</h4>
                    @if($isConfigured && $is_enabled)
                        <span class="badge bg-success">Active &amp; Configured</span>
                    @elseif($isConfigured)
                        <span class="badge bg-warning text-dark">Configured (Disabled)</span>
                    @else
                        <span class="badge bg-secondary">Setup Incomplete</span>
                    @endif
                </div>
                <p class="text-muted small mb-0">
                    Implements OPay RSA encryption (<code>paramContent</code>) &amp; SHA256withRSA signing (<code>sign</code>) per
                    <a href="https://documentation.opayweb.com/doc/offline/authentication.html" target="_blank" rel="noopener noreferrer">OPay Offline Authentication Docs</a>.
                </p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="form-check form-switch fs-5 mb-0">
                    <input class="form-check-input" type="checkbox" role="switch" id="opayEnabledSwitch" wire:model="is_enabled" @disabled(!$canEditOpay)>
                    <label class="form-check-label fs-6 fw-bold" for="opayEnabledSwitch">{{ $is_enabled ? 'Enabled' : 'Disabled' }}</label>
                </div>
            </div>
        </div>
    </div>

    @if($diagnosticResult)
        <div class="alert {{ $diagnosticResult['ok'] ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show mb-4" role="alert">
            <strong>{{ $diagnosticResult['title'] }}:</strong> {{ $diagnosticResult['message'] }}
            <button type="button" class="btn-close" wire:click="$set('diagnosticResult', null)" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- ── LEFT COLUMN: Step 1 & Step 2 (Values to Copy TO OPay Dashboard) ── --}}
        <div class="col-lg-6 col-12">
            {{-- Step 1: Merchant RSA Key Pair --}}
            <div class="card shadow-none border mb-4">
                <div class="card-header bg-body-tertiary py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-primary me-2">Step 1</span>
                        <span class="fw-bold">Merchant RSA Key Pair</span>
                    </div>
                    @if($has_merchant_private_key)
                        <span class="badge bg-success"><i class="fa fa-lock me-1"></i>Private Key Encrypted</span>
                    @else
                        <span class="badge bg-warning text-dark">Not Generated</span>
                    @endif
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Generate your Merchant RSA key pair below, then copy the <strong>Merchant Public Key</strong> into the
                        <em>POS &amp; Others → Merchant Public Key</em> field on your OPay Business Dashboard.
                        Your private key is encrypted at rest and never exposed in the browser.
                    </p>

                    <div class="row g-2 align-items-end mb-3">
                        <div class="col-sm-5">
                            <label class="form-label small fw-bold">RSA Key Size</label>
                            <select wire:model="key_bits" class="form-select form-select-sm" @disabled(!$canEditOpay)>
                                <option value="1024">1024-bit (OPay Docs Default)</option>
                                <option value="2048">2048-bit</option>
                            </select>
                        </div>
                        @if($canEditOpay)
                            <div class="col-sm-7 text-sm-end">
                                <button
                                    type="button"
                                    wire:click="generateKeyPair"
                                    @if($has_merchant_private_key) wire:confirm="Generating a new RSA key pair will replace the existing keys. You will need to update the Merchant Public Key on your OPay Dashboard. Continue?" @endif
                                    class="btn btn-sm btn-primary"
                                >
                                    <i wire:loading.remove wire:target="generateKeyPair" class="fa fa-key me-1"></i>
                                    <span wire:loading wire:target="generateKeyPair" class="spinner-border spinner-border-sm me-1" role="status"></span>
                                    {{ $has_merchant_private_key ? 'Regenerate RSA Key Pair' : 'Generate RSA Key Pair' }}
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold mb-0">Merchant Public Key <span class="text-danger">*</span></label>
                            <div class="d-flex gap-2">
                                @if($merchant_public_key_raw)
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="copyOpayField('opayMerchantPublicKeyRaw', this)">
                                        <i class="fa fa-copy me-1"></i>Copy for OPay
                                    </button>
                                    <button type="button" wire:click="downloadPublicKey" class="btn btn-sm btn-outline-secondary py-0 px-2">
                                        <i class="fa fa-download me-1"></i>.pem
                                    </button>
                                @endif
                            </div>
                        </div>
                        <textarea
                            id="opayMerchantPublicKeyRaw"
                            class="form-control font-monospace small bg-body-tertiary"
                            rows="4"
                            readonly
                            placeholder="Click 'Generate RSA Key Pair' above to create your Merchant Public Key..."
                        >{{ $merchant_public_key_raw }}</textarea>
                        <small class="text-muted">
                            Formatted as single-line Base64 (no PEM headers) for direct paste into OPay Dashboard.
                            @if($keys_generated_at)
                                <span class="ms-1">Generated: {{ $keys_generated_at }}</span>
                            @endif
                        </small>
                    </div>

                    @if($canEditOpay)
                        <div class="border-top pt-3">
                            <button type="button" wire:click="$toggle('showImportKeys')" class="btn btn-link btn-sm p-0 text-decoration-none">
                                <i class="fa fa-upload me-1"></i>{{ $showImportKeys ? 'Hide manual key import' : 'Already generated keys via OpenSSL CLI? Import custom key pair' }}
                            </button>

                            @if($showImportKeys)
                                <div class="mt-3 p-3 bg-body-tertiary rounded border">
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Merchant Public Key (PEM or Base64)</label>
                                        <textarea wire:model="import_merchant_public_key" class="form-control form-control-sm font-monospace" rows="3" placeholder="-----BEGIN PUBLIC KEY----- ..."></textarea>
                                        @error('import_merchant_public_key') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Merchant Private Key (PEM or Base64 PKCS#8)</label>
                                        <textarea wire:model="import_merchant_private_key" class="form-control form-control-sm font-monospace" rows="4" placeholder="-----BEGIN PRIVATE KEY----- ..."></textarea>
                                        @error('import_merchant_private_key') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <button type="button" wire:click="importKeyPair" class="btn btn-sm btn-dark">
                                        <i class="fa fa-check me-1"></i>Verify &amp; Import Key Pair
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Step 2: IP WhiteListing & Webhook URL --}}
            <div class="card shadow-none border">
                <div class="card-header bg-body-tertiary py-3">
                    <span class="badge bg-primary me-2">Step 2</span>
                    <span class="fw-bold">IP WhiteListing &amp; Webhook URL (Copy to OPay Dashboard)</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold mb-0">IP WhiteListing <span class="text-danger">*</span></label>
                            <div class="d-flex gap-2">
                                @if($detected_server_ip && $canEditOpay)
                                    <button type="button" wire:click="appendDetectedServerIp" class="btn btn-sm btn-outline-info py-0 px-2">
                                        + Add Server IP ({{ $detected_server_ip }})
                                    </button>
                                @endif
                                @if($ip_whitelisting)
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="copyOpayField('opayIpWhitelist', this)">
                                        <i class="fa fa-copy me-1"></i>Copy
                                    </button>
                                @endif
                            </div>
                        </div>
                        <input
                            id="opayIpWhitelist"
                            type="text"
                            wire:model="ip_whitelisting"
                            class="form-control font-monospace"
                            placeholder="11.11.11.11,22.22.22.22"
                            @disabled(!$canEditOpay)
                        >
                        @error('ip_whitelisting') <span class="text-danger small">{{ $message }}</span> @enderror
                        <small class="text-muted">
                            Comma-separated public outbound IP address(es) of this server to authorize on the OPay Dashboard.
                        </small>
                    </div>

                    <div class="mb-0">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold mb-0">Webhook URL</label>
                            <div class="d-flex gap-2">
                                @if($canEditOpay)
                                    <button type="button" wire:click="resetWebhookUrl" class="btn btn-sm btn-outline-secondary py-0 px-2">
                                        Use Default
                                    </button>
                                @endif
                                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="copyOpayField('opayWebhookUrl', this)">
                                    <i class="fa fa-copy me-1"></i>Copy
                                </button>
                            </div>
                        </div>
                        <input
                            id="opayWebhookUrl"
                            type="url"
                            wire:model="webhook_url"
                            class="form-control font-monospace"
                            placeholder="{{ $defaultWebhookUrl }}"
                            @disabled(!$canEditOpay)
                        >
                        @error('webhook_url') <span class="text-danger small">{{ $message }}</span> @enderror
                        <small class="text-muted">
                            Paste this publicly accessible HTTPS URL into OPay Dashboard → <em>Webhook URL</em>. Responds with <code>{"code":"00000","message":"SUCCESSFUL"}</code>.
                        </small>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── RIGHT COLUMN: Step 3 (Credentials FROM OPay Dashboard) & Connection ── --}}
        <div class="col-lg-6 col-12">
            <div class="card shadow-none border mb-4">
                <div class="card-header bg-body-tertiary py-3">
                    <span class="badge bg-success me-2">Step 3</span>
                    <span class="fw-bold">OPay Dashboard Credentials</span>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        After saving your Merchant Public Key, IP WhiteListing, and Webhook URL on OPay's Dashboard,
                        OPay will generate your <strong>OPay Public Key</strong> and <strong>Client Auth Key</strong>. Paste them here.
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-bold">OPay Public Key</label>
                        <textarea
                            wire:model="opay_public_key"
                            class="form-control font-monospace small"
                            rows="4"
                            placeholder="Paste OPay Public Key from OPay Dashboard (Base64 or PEM)..."
                            @disabled(!$canEditOpay)
                        ></textarea>
                        @error('opay_public_key') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold mb-0">Client Auth Key</label>
                            @if($has_existing_client_auth_key)
                                <span class="badge bg-success-subtle text-success border">
                                    Saved: <code>{{ $client_auth_key_masked }}</code>
                                </span>
                            @endif
                        </div>
                        <div class="input-group">
                            <input
                                type="password"
                                wire:model="client_auth_key"
                                class="form-control font-monospace"
                                autocomplete="new-password"
                                placeholder="{{ $has_existing_client_auth_key ? 'Leave blank to keep existing Client Auth Key' : 'Paste 32-character Client Auth Key from OPay' }}"
                                @disabled(!$canEditOpay)
                            >
                            @if($has_existing_client_auth_key && $canEditOpay)
                                <button
                                    type="button"
                                    wire:click="clearClientAuthKey"
                                    wire:confirm="Clear the saved Client Auth Key?"
                                    class="btn btn-outline-danger"
                                >
                                    Clear
                                </button>
                            @endif
                        </div>
                        @error('client_auth_key') <span class="text-danger small">{{ $message }}</span> @enderror
                        <small class="text-muted">Encrypted at rest. Sent in the <code>clientAuthKey</code> request header.</small>
                    </div>

                    <hr class="my-3">

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Head Merchant ID</label>
                            <input type="text" wire:model="head_merchant_id" class="form-control font-monospace" placeholder="e.g. 256622092286390" @disabled(!$canEditOpay)>
                            @error('head_merchant_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Merchant / Branch ID</label>
                            <input type="text" wire:model="merchant_id" class="form-control font-monospace" placeholder="e.g. 256622092286391" @disabled(!$canEditOpay)>
                            @error('merchant_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Environment</label>
                            <select wire:model="environment" class="form-select" @disabled(!$canEditOpay)>
                                <option value="live">Live (Production)</option>
                                <option value="sandbox">Sandbox / Test</option>
                            </select>
                            @error('environment') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold">API Base URL</label>
                            <input type="url" wire:model="base_url" class="form-control font-monospace" placeholder="https://payapi.opayweb.com" @disabled(!$canEditOpay)>
                            @error('base_url') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Version</label>
                            <input type="text" wire:model="api_version" class="form-control font-monospace" placeholder="V1.0.1" @disabled(!$canEditOpay)>
                            @error('api_version') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    @if($canEditOpay)
                        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mt-4 pt-2 border-top">
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" wire:click="verifyCrypto" class="btn btn-outline-secondary btn-sm">
                                    <i wire:loading.remove wire:target="verifyCrypto" class="fa fa-shield-alt me-1"></i>
                                    <span wire:loading wire:target="verifyCrypto" class="spinner-border spinner-border-sm me-1" role="status"></span>
                                    Verify Keys &amp; Crypto
                                </button>
                                <button type="button" wire:click="testConnection" class="btn btn-outline-success btn-sm" @disabled(!$isConfigured)>
                                    <i wire:loading.remove wire:target="testConnection" class="fa fa-plug me-1"></i>
                                    <span wire:loading wire:target="testConnection" class="spinner-border spinner-border-sm me-1" role="status"></span>
                                    Test Live Connection
                                </button>
                            </div>

                            <button type="button" wire:click="save" class="btn btn-primary">
                                <i wire:loading.remove wire:target="save" class="fa fa-save me-1"></i>
                                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-1" role="status"></span>
                                Save OPay Settings
                            </button>
                        </div>
                    @endif

                    @if($last_tested_at)
                        <div class="mt-3 p-2 rounded bg-body-tertiary border small">
                            <strong>Last Live Test ({{ $last_tested_at }}):</strong>
                            <span class="badge {{ $last_test_status === 'success' ? 'bg-success' : 'bg-danger' }} ms-1">
                                {{ strtoupper((string) $last_test_status) }}
                            </span>
                            <div class="text-muted mt-1">{{ $last_test_message }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── Recent Incoming OPay Webhooks ──────────────────────────────────── --}}
    <div class="card shadow-none border mt-4">
        <div class="card-header bg-body-tertiary py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fa fa-exchange-alt me-2 text-primary"></i>Recent OPay Webhooks</h5>
            <span class="text-muted small">Latest 10 notifications received at <code>POST /api/opay/webhook</code></span>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0 small">
                <thead class="bg-body-tertiary">
                    <tr>
                        <th>Received At</th>
                        <th>Transaction ID</th>
                        <th>Out Order No</th>
                        <th>Order No</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Signature</th>
                        <th>Kafka Forward</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentWebhooks as $log)
                        <tr wire:key="opay-wh-{{ $log->id }}">
                            <td>{{ $log->created_at?->format('Y-m-d H:i:s') }}</td>
                            <td class="font-monospace">{{ $log->transaction_id ?: '—' }}</td>
                            <td class="font-monospace">{{ $log->out_order_no ?: '—' }}</td>
                            <td class="font-monospace">{{ $log->order_no ?: '—' }}</td>
                            <td>{{ $log->currency }} {{ $log->amount ?: '—' }}</td>
                            <td>
                                @if($log->status === 'SUCCESS')
                                    <span class="badge bg-success">{{ $log->status }}</span>
                                @elseif($log->status)
                                    <span class="badge bg-secondary">{{ $log->status }}</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if($log->signature_verified === true)
                                    <span class="badge bg-success">Verified</span>
                                @elseif($log->signature_verified === false)
                                    <span class="badge bg-danger">Invalid</span>
                                @else
                                    <span class="text-muted">Plaintext</span>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                @if($log->kafka_status === 'sent')
                                    <span class="badge bg-success">Sent</span>
                                @elseif($log->kafka_status === 'failed')
                                    <span class="badge bg-danger" title="{{ $log->kafka_error }}">Failed</span>
                                @else
                                    <span class="badge bg-secondary">Pending</span>
                                @endif
                                @if($canEditOpay)
                                    <button
                                        type="button"
                                        wire:click="resendWebhookToKafka({{ $log->id }})"
                                        class="btn btn-sm btn-outline-secondary py-0 px-1 ms-1"
                                        title="Resend this webhook to PSGeneralDrugCentreKafka via Kafka"
                                    >
                                        <i class="fa fa-redo"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No OPay webhooks received yet. Configure the Webhook URL above in your OPay Dashboard to begin receiving notifications.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function copyOpayField(elementId, button) {
        const el = document.getElementById(elementId);
        if (!el) return;
        const text = el.value !== undefined ? el.value : el.textContent;
        if (!text) return;

        const finish = () => {
            const original = button.innerHTML;
            button.innerHTML = '<i class="fa fa-check me-1"></i>Copied';
            setTimeout(() => { button.innerHTML = original; }, 1800);
        };

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(finish);
        } else {
            el.select();
            document.execCommand('copy');
            finish();
        }
    }
</script>
