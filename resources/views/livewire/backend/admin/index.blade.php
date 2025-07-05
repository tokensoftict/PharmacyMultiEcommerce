<?php

use App\Services\DashboardService;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new  class extends Component {

  public int $orderAwaitingProcess = 0;
  public int $orderAwaitingPayment = 0;
  public int $productOutOfStock = 0;

  public int $totalNewCustomersLastSevenDays = 0;


  public array $barChartData, $stackedChartData, $customerChartData = [];


  public function mount()
  {
    $dashboardService = app(DashboardService::class);
    $this->barChartData = $dashboardService->getMonthlySalesData();
    $this->stackedChartData = $dashboardService->getLast7DaysStatusBreakdown();
    $this->customerChartData = $dashboardService->getNewCustomersComparisonWithStats();
    $this->totalNewCustomersLastSevenDays = $this->customerChartData['totals']['this_month'];
  }

}
?>
@push('js')
  <script src="{{ asset('vendor/echarts/echarts.min.js') }}"></script>
  <script src="{{ asset('vendor/leaflet/leaflet.js') }}"></script>
  <script src="{{ asset('vendor/leaflet.markercluster/leaflet.markercluster.js') }}"></script>
  <script src="{{ asset('vendor/leaflet.tilelayer.colorfilter/leaflet-tilelayer-colorfilter.min.js') }}"></script>
  <script>
    $(document).ready(function () {
      const chartData = @json($this->barChartData);
      var chart = echarts.init(document.getElementById('echart-basic-bar-chart-example'));
      var option = {
        tooltip: {},
        xAxis: {
          type: 'category',
          data: chartData.labels
        },
        yAxis: {
          type: 'value'
        },
        series: [
          {
            name: 'Sales',
            type: 'bar',
            data: chartData.data,
            itemStyle: {
              color: '#3874FF'
            }
          }
        ]
      };
      chart.setOption(option);



      const stackedChartData = @json($this->stackedChartData);
      var stackedChart = echarts.init(document.getElementById('echart-total-orders'));
      // Chart Options
      var stackedOption = {
        tooltip: {},
        legend: {
          data: ['', '']
        },
        xAxis: {
          type: 'category',
          data: stackedChartData.labels,
          axisLine: {show: false},
          axisTick: {show: false},
          axisLabel: {show: false}
        },
        yAxis: {
          type: 'value',
          max: 100,
          axisLine: {show: false},
          axisTick: {show: false},
          axisLabel: {show: false},
          splitLine: {show: false}
        },
        series: [
          {
            name: 'Completed',
            type: 'bar',
            stack: 'total',
            data: stackedChartData.completed,
            barWidth: '10%',
            itemStyle: {
              color: '#4CAF50'
            },
            emphasis: {disabled: false}
          },
          {
            name: 'Pending Payment',
            type: 'bar',
            stack: 'total',
            data: stackedChartData.pending,
            barWidth: '10%',
            itemStyle: {
              color: '#FF9800'
            }
          }
        ]
      };
      // Set the options
      stackedChart.setOption(stackedOption);

      const customerChartData = @json($customerChartData);

      const customerCharts = echarts.init(document.getElementById('echarts-new-customers'));

      const customerOption = {
        tooltip: {
          trigger: "axis",
          padding: 10,
          backgroundColor: window.phoenix.utils.getColor("body-highlight-bg"),
          borderColor: window.phoenix.utils.getColor("border-color"),
          textStyle: {color: window.phoenix.utils.getColor("light-text-emphasis")},
          borderWidth: 1,
          transitionDuration: 0,
          axisPointer: {type: "none"},

        },
        xAxis: [
          {
            type: "category",
            data: customerChartData.dates,
            show: true,
            boundaryGap: false,
            axisLine: {show: true, lineStyle: {color: window.phoenix.utils.getColor("secondary-bg")}},
            axisTick: {show: false},
            axisLabel: {
              formatter: (o) => window.dayjs(o).format("DD MMM"),
              color: window.phoenix.utils.getColor("secondary-color"),
              align: "left",
              interval: 5,
              fontFamily: "Nunito Sans",
              fontWeight: 600,
              fontSize: 12.8,
            },
          },
          {
            type: "category",
            position: "bottom",
            data: customerChartData.dates,
            axisLabel: {
              formatter: (o) => window.dayjs(o).format("DD MMM"),
              interval: 130,
              color: window.phoenix.utils.getColor("secondary-color"),
              align: "right",
              fontFamily: "Nunito Sans",
              fontWeight: 600,
              fontSize: 12.8,
            },
            axisLine: {show: false},
            axisTick: {show: false},
            splitLine: {show: false},
            boundaryGap: false,
          },
        ],
        yAxis: {
          show: false,
          type: "value",
          boundaryGap: false
        },
        series: [
          {
            type: "line",
            data: customerChartData.last_month,
            showSymbol: false,
            symbol: "circle",
            lineStyle: {width: 2, color: window.phoenix.utils.getColor("secondary-bg")},
            emphasis: {lineStyle: {color: window.phoenix.utils.getColor("secondary-bg")}}
          },
          {
            type: "line",
            data: customerChartData.this_month,
            lineStyle: {width: 2, color: window.phoenix.utils.getColor("primary")},
            showSymbol: false,
            symbol: "circle"
          },
        ],
        grid: {left: 0, right: 0, top: 5, bottom: 20},
      };

      customerCharts.setOption(customerOption);
    });
  </script>
@endpush
<div>
  <div class="pb-2">
    <div class="row g-4">
      <div class="col-12 col-xxl-6">
        <div class="mb-5 mt-7">
          <h2 class="mb-2">Sales Overview</h2>
          <h5 class="text-body-tertiary fw-semibold">Key insights into sales trends and performance metrics.</h5>
        </div>

        <div class="row align-items-center g-4">
          <div class="col-12 col-md-auto">
            <div class="d-flex align-items-center"><span class="fa-stack" style="min-height: 46px;min-width: 46px;">
                <span class="fa-solid fa-square fa-stack-2x dark__text-opacity-50 text-success-light"
                      data-fa-transform="down-4 rotate--10 left-4"></span>
                <span class="fa-solid fa-circle fa-stack-2x stack-circle text-stats-circle-success"
                      data-fa-transform="up-4 right-3 grow-2"></span><span
                        class="fa-stack-1x fa-solid fa-star text-success "
                        data-fa-transform="shrink-2 up-8 right-6"></span></span>
              <div class="ms-3">
                <h4 class="mb-0">{{ formatNumber($this->orderAwaitingProcess) }} new orders</h4>
                <p class="text-body-secondary fs-9 mb-0">Awaiting processing</p>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-auto">
            <div class="d-flex align-items-center"><span class="fa-stack"
                                                         style="min-height: 46px;min-width: 46px;"><span
                        class="fa-solid fa-square fa-stack-2x dark__text-opacity-50 text-warning-light"
                        data-fa-transform="down-4 rotate--10 left-4"></span><span
                        class="fa-solid fa-circle fa-stack-2x stack-circle text-stats-circle-warning"
                        data-fa-transform="up-4 right-3 grow-2"></span><span
                        class="fa-stack-1x fa-solid fa-pause text-warning "
                        data-fa-transform="shrink-2 up-8 right-6"></span></span>
              <div class="ms-3">
                <h4 class="mb-0">{{ formatNumber($this->orderAwaitingPayment) }} orders</h4>
                <p class="text-body-secondary fs-9 mb-0">Waiting For Payment</p>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-auto">
            <div class="d-flex align-items-center"><span class="fa-stack"
                                                         style="min-height: 46px;min-width: 46px;"><span
                        class="fa-solid fa-square fa-stack-2x dark__text-opacity-50 text-danger-light"
                        data-fa-transform="down-4 rotate--10 left-4"></span><span
                        class="fa-solid fa-circle fa-stack-2x stack-circle text-stats-circle-danger"
                        data-fa-transform="up-4 right-3 grow-2"></span><span
                        class="fa-stack-1x fa-solid fa-xmark text-danger "
                        data-fa-transform="shrink-2 up-8 right-6"></span></span>
              <div class="ms-3">
                <h4 class="mb-0">{{ formatNumber($this->productOutOfStock) }} products</h4>
                <p class="text-body-secondary fs-9 mb-0">Out of stock</p>
              </div>
            </div>
          </div>
        </div>

      </div>
      <div class="col-12 col-xxl-6">
        <div class="row g-3">
          <div class="col-12 col-md-6">
            <div class="card h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between">
                  <div>
                    <h5 class="mb-1">Total orders<span
                              class="badge badge-phoenix badge-phoenix-warning rounded-pill fs-9 ms-2"><span
                                class="badge-label">{{ $this->stackedChartData['percentages']['difference'] }}%</span></span></h5>
                    <h6 class="text-body-tertiary">Last 7 days</h6>
                  </div>
                  <h4>{{ money($this->stackedChartData['totals']['amount']) }}</h4>
                </div>
                <div class="d-flex justify-content-center">
                  <div class="echart-total-orders" id="echart-total-orders" style="height:150px;width:100%"></div>
                </div>
                <div class="mt-2">
                  <div class="d-flex align-items-center mb-2">
                    <div class="bullet-item me-2" style="background: #4CAF50;"></div>
                    <h6 class="text-body fw-semibold flex-1 mb-0">Completed</h6>
                    <h6 class="text-body fw-semibold mb-0">{{ $this->stackedChartData['percentages']['completed'] }}%</h6>
                  </div>
                  <div class="d-flex align-items-center">
                    <div class="bullet-item me-2" style="background: #FF9800"></div>
                    <h6 class="text-body fw-semibold flex-1 mb-0">Pending payment</h6>
                    <h6 class="text-body fw-semibold mb-0">{{ $this->stackedChartData['percentages']['pending'] }}%</h6>
                  </div>
                </div>
              </div>
            </div>


          </div>
          <div class="col-12 col-md-6">
            <div class="card h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between">
                  <div>
                    <h5 class="mb-1">New customers<span
                              class="badge badge-phoenix badge-phoenix-warning rounded-pill fs-9 ms-2"> <span
                                class="badge-label">{{ $this->customerChartData['percentage_difference'] }}</span></span></h5>
                    <h6 class="text-body-tertiary">Last 7 days</h6>
                  </div>
                  <h4>{{ formatNumber($this->totalNewCustomersLastSevenDays) }}</h4>
                </div>
                <div class="pb-0 pt-4">
                  <div class="echarts-new-customers" id="echarts-new-customers" style="height:180px;width:100%;"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <hr class="bg-body-secondary mb-6 mt-6"/>
    <div class="row flex-between-center mb-4 g-3">
      <div class="col-12 col-xxl-12 col-sm-12 col-lg-12">
        <h3>Total Sales By Month</h3>
        <p class="text-body-tertiary lh-sm mb-0">Total Sales By Each Month For This Year {{ date('Y') }}</p>
        <div class="echart-basic-bar-chart-example" id="echart-basic-bar-chart-example" style="min-height:300px"></div>
      </div>
    </div>
  </div>

  <div class="mx-n1 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis pt-5 border-y">

    <div class="row align-items-end justify-content-between pb-5 g-3">
      <div class="col-auto">
        <h3>Latest orders</h3>
        <p class="text-body-tertiary lh-sm mb-0">Latest 20 orders received</p>
      </div>
    </div>

    <div class="colsm-12 col-lg-12 col-12">

      <div class="table-responsive table-reponsive-sm scrollbar">
        <table class="table">
          <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Invoice No.</th>
            <th scope="col">Order ID</th>
            <th scope="col">First name</th>
            <th scope="col">Last name</th>
            <th scope="col">Status</th>
            <th scope="col">Business Name</th>
            <th scope="col">Telephone</th>
            <th scope="col">No Of Items</th>
            <th scope="col">Date</th>
            <th scope="col">Total</th>
            <th scope="col">Action</th>
          </tr>
          </thead>
          <tbody>

          </tbody>
        </table>
      </div>

    </div>

  </div>
</div>