<?php

namespace Tests\Feature;

use App\Models\App;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Stock;
use App\Services\Order\CreateOrderProductService;
use App\Classes\ApplicationEnvironment;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OrderProductOptionsTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // Safely bootstrap the ApplicationEnvironment if an App exists
        $app = App::first();
        if ($app) {
            ApplicationEnvironment::createApplicationEnvironment($app);
        }
    }

    /**
     * Test that the OrderProduct model correctly casts options to and from an array.
     */
    public function test_order_product_options_cast_correctly(): void
    {
        $order = Order::first();
        $stock = Stock::first();

        if (!$order || !$stock) {
            $this->markTestSkipped('No order/stock items available in the database for testing.');
        }

        $optionsPayload = [
            [
                'id' => 101,
                'name' => 'Color',
                'value' => 'Crimson Red',
                'price' => 250.0,
                'price_prefix' => '+',
                'group_name' => 'Color',
                'value_name' => 'Crimson Red',
                'option_name' => 'Color',
                'selectedValue' => 'Crimson Red',
                'amount' => 250.0,
                'sign' => '+'
            ],
            [
                'id' => 102,
                'name' => 'Size',
                'value' => 'XXL',
                'price' => 0.0,
                'price_prefix' => '+',
                'group_name' => 'Size',
                'value_name' => 'XXL',
                'option_name' => 'Size',
                'selectedValue' => 'XXL',
                'amount' => 0.0,
                'sign' => '+'
            ]
        ];

        // 3. Create OrderProduct
        $orderProduct = OrderProduct::create([
            'order_product_id' => 'TEST-OP-' . uniqid(),
            'order_id' => $order->id,
            'stock_id' => $stock->id,
            'local_id' => $stock->local_stock_id ?? 1,
            'name' => $stock->name,
            'model' => $stock->model ?? 'TEST-MODEL',
            'quantity' => 2,
            'price' => 1500.0,
            'total' => 3000.0,
            'tax' => 0,
            'reward' => 10,
            'options' => $optionsPayload,
        ]);

        // Retrieve from database and assert
        $retrieved = OrderProduct::find($orderProduct->id);
        $this->assertNotNull($retrieved);
        $this->assertIsArray($retrieved->options);
        $this->assertCount(2, $retrieved->options);
        $this->assertEquals('Crimson Red', $retrieved->options[0]['value']);
        $this->assertEquals(250.0, $retrieved->options[0]['price']);
        $this->assertEquals('XXL', $retrieved->options[1]['value']);
        
        // Assert Kafka-compatible keys are casted/saved correctly
        $this->assertEquals('Color', $retrieved->options[0]['option_name']);
        $this->assertEquals('Crimson Red', $retrieved->options[0]['selectedValue']);
        $this->assertEquals(250.0, $retrieved->options[0]['amount']);
        $this->assertEquals('+', $retrieved->options[0]['sign']);
    }

    /**
     * Test that order product helper resolve logic works under different app environments.
     */
    public function test_options_resolution_helper(): void
    {
        $stock = Stock::first();
        if (!$stock) {
            $this->markTestSkipped('No stock items available in the database for testing.');
        }

        $service = new CreateOrderProductService();
        
        // Assert that calling resolveSelectedOptions with empty IDs returns an empty array
        $resolved = $this->runPrivateMethod($service, 'resolveSelectedOptions', [$stock, []]);
        $this->assertIsArray($resolved);
        $this->assertEmpty($resolved);
    }

    /**
     * Helper to run private methods on objects.
     */
    protected function runPrivateMethod($object, string $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
