<?php

use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new  class extends Component {

  public int $orderAwaitingProcess = 0;
  public int $orderAwaitingPayment = 0;
  public int $productOutOfStock = 0;

  public int $totalNumberOfOrdersLastSevenDays = 0;
  public int $totalNewCustomersLastSevenDays = 0;

  public array $sevenDaysFromTodayOfThisMonthDateRange = [];
  public array $sevenDaysFromTodayOfLastMonthDateRange = [];



  public function mount()
  {
    // 7-day range starting from today (this month)
    $startToday = now()->startOfDay();
    $endToday = now()->copy()->addDays(6)->endOfDay();
    $endOfThisMonth = now()->endOfMonth();
    $this->sevenDaysFromTodayOfThisMonthDateRange = [$startToday->toDateString(), $endToday->lt($endOfThisMonth) ? $endToday->toDateString() : $endOfThisMonth->toDateString()];

    // 7-day range starting from same day last month
    $sameDayLastMonth = now()->subMonth()->startOfDay();
    $startLastMonth = Carbon::createFromDate($sameDayLastMonth->year, $sameDayLastMonth->month, now()->day)->startOfDay();
    $endOfLastMonth = $startLastMonth->copy()->addDays(6)->endOfDay();
    $lastDayOfLastMonth = $startLastMonth->copy()->endOfMonth();
    $this->sevenDaysFromTodayOfLastMonthDateRange = [$startLastMonth->toDateString(), $endOfLastMonth->lt($lastDayOfLastMonth) ? $endOfLastMonth->toDateString() : $lastDayOfLastMonth->toDateString()];


  }

}
?>
@push('js')
  <script src="{{ asset('vendor/echarts/echarts.min.js') }}"></script>
  <script src="{{ asset('vendor/leaflet/leaflet.js') }}"></script>
  <script src="{{ asset('vendor/leaflet.markercluster/leaflet.markercluster.js') }}"></script>
  <script src="{{ asset('vendor/leaflet.tilelayer.colorfilter/leaflet-tilelayer-colorfilter.min.js') }}"></script>
  <script>
    $(document).ready(function() {
      var chart = echarts.init(document.getElementById('echart-basic-bar-chart-example'));
      // Chart Options
      var option = {
        tooltip: {},
        xAxis: {
          type: 'category',
          data: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']
        },
        yAxis: {
          type: 'value'
        },
        series: [
          {
            name: 'Sales',
            type: 'bar',
            data: [120, 200, 150, 80, 70, 110],
            itemStyle: {
              color: '#3874FF'
            }
          }
        ]
      };
      // Set the options
      chart.setOption(option);

      var stackedChart = echarts.init(document.getElementById('echart-total-orders'));
      // Example values out of 100
      var values = [40, 60, 30, 20, 25, 50, 55];
      var stackedOption = {
        grid: {
          left: 10,
          right: 10,
          bottom: 10,
          top: 10,
          containLabel: false
        },
        xAxis: {
          type: 'category',
          data: ['', '', '', '', '', '', ''],
          axisLine: { show: false },
          axisTick: { show: false },
          axisLabel: { show: false }
        },
        yAxis: {
          type: 'value',
          max: 100,
          axisLine: { show: false },
          axisTick: { show: false },
          axisLabel: { show: false },
          splitLine: { show: false }
        },
        series: [
          {
            name: 'Empty',
            type: 'bar',
            stack: 'total',
            data: values.map(v => 100 - v),
            barWidth: '10%',
            itemStyle: {
              color: '#2563EB'
            },
            emphasis: { disabled: false }
          },
          {
            name: 'Filled',
            type: 'bar',
            stack: 'total',
            data: values,
            barWidth: '10%',
            itemStyle: {
              color: '#E0E7FF'
            }
          }
        ]
      };
      stackedChart.setOption(stackedOption);




      const customerCharts = echarts.init(document.getElementById('echarts-new-customers'));

      // Simulated labels for last 7 days from today
      const days = ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'];

      // Replace these with your dynamic values
      const thisMonthData = [5, 10, 8, 6, 12, 15, 20];  // e.g. April 9–15
      const lastMonthData = [3, 7, 5, 6, 7, 6, 8];       // e.g. March 9–15






      const i = (o) => {
        const t = window.dayjs(o[0].axisValue),
                e = window.dayjs(o[0].axisValue).subtract(1, "month"),
                a = o.map((o, a) => ({ value: o.value, date: a > 0 ? e : t, color: o.color }));
        let i = "";
        return (
                a.forEach((o, t) => {
                  i += `<h6 class="fs-9 text-body-tertiary ${t > 0 && "mb-0"}"><span class="fas fa-circle me-2" style="color:${o.color}"></span>\n      ${o.date.format("MMM DD")} : ${o.value}\n    </h6>`;
                }),
                        `<div class='ms-1'>\n              ${i}\n            </div>`
        );
      };

      const customerOption = {
        tooltip: {
          trigger: "axis",
          padding: 10,
          backgroundColor: window.phoenix.utils.getColor("body-highlight-bg"),
          borderColor: window.phoenix.utils.getColor("border-color"),
          textStyle: { color: window.phoenix.utils.getColor("light-text-emphasis") },
          borderWidth: 1,
          transitionDuration: 0,
          axisPointer: { type: "none" },
          formatter: i,
        },
        xAxis: [
          {
            type: "category",
            data: window.phoenix.utils.getDates(new Date("5/1/2022"), new Date("5/7/2022"), 864e5),
            show: !0,
            boundaryGap: !1,
            axisLine: { show: !0, lineStyle: { color: window.phoenix.utils.getColor("secondary-bg") } },
            axisTick: { show: !1 },
            axisLabel: {
              formatter: (o) => window.dayjs(o).format("DD MMM"),
              showMinLabel: !0,
              showMaxLabel: !1,
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
            show: !0,
            data:  window.phoenix.utils.getDates(new Date("5/1/2022"), new Date("5/7/2022"), 864e5),
            axisLabel: {
              formatter: (o) => window.dayjs(o).format("DD MMM"),
              interval: 130,
              showMaxLabel: !0,
              showMinLabel: !1,
              color:  window.phoenix.utils.getColor("secondary-color"),
              align: "right",
              fontFamily: "Nunito Sans",
              fontWeight: 600,
              fontSize: 12.8,
            },
            axisLine: { show: !1 },
            axisTick: { show: !1 },
            splitLine: { show: !1 },
            boundaryGap: !1,
          },
        ],
        yAxis: { show: !1, type: "value", boundaryGap: !1 },
        series: [
          { type: "line", data: [150, 100, 300, 200, 250, 180, 250], showSymbol: !1, symbol: "circle", lineStyle: { width: 2, color:  window.phoenix.utils.getColor("secondary-bg") }, emphasis: { lineStyle: { color: window.phoenix.utils.getColor("secondary-bg") } } },
          { type: "line", data: [200, 150, 250, 100, 500, 400, 600], lineStyle: { width: 2, color: window.phoenix.utils.getColor("primary") }, showSymbol: !1, symbol: "circle" },
        ],
        grid: { left: 0, right: 0, top: 5, bottom: 20 },
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
                <span class="fa-solid fa-square fa-stack-2x dark__text-opacity-50 text-success-light" data-fa-transform="down-4 rotate--10 left-4"></span>
                <span class="fa-solid fa-circle fa-stack-2x stack-circle text-stats-circle-success" data-fa-transform="up-4 right-3 grow-2"></span><span class="fa-stack-1x fa-solid fa-star text-success " data-fa-transform="shrink-2 up-8 right-6"></span></span>
              <div class="ms-3">
                <h4 class="mb-0">{{ formatNumber($this->orderAwaitingProcess) }} new orders</h4>
                <p class="text-body-secondary fs-9 mb-0">Awaiting processing</p>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-auto">
            <div class="d-flex align-items-center"><span class="fa-stack" style="min-height: 46px;min-width: 46px;"><span
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
            <div class="d-flex align-items-center"><span class="fa-stack" style="min-height: 46px;min-width: 46px;"><span
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
                                class="badge-label">-6.8%</span></span></h5>
                    <h6 class="text-body-tertiary">Last 7 days</h6>
                  </div>
                  <h4>{{ money($this->totalNumberOfOrdersLastSevenDays) }}</h4>
                </div>
                <div class="d-flex justify-content-center">
                  <div class="echart-total-orders" id="echart-total-orders" style="height:150px;width:100%"></div>
                </div>
                <div class="mt-2">
                  <div class="d-flex align-items-center mb-2">
                    <div class="bullet-item bg-primary me-2"></div>
                    <h6 class="text-body fw-semibold flex-1 mb-0">Completed</h6>
                    <h6 class="text-body fw-semibold mb-0">52%</h6>
                  </div>
                  <div class="d-flex align-items-center">
                    <div class="bullet-item bg-primary-subtle me-2"></div>
                    <h6 class="text-body fw-semibold flex-1 mb-0">Pending payment</h6>
                    <h6 class="text-body fw-semibold mb-0">48%</h6>
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
                                class="badge-label">+26.5%</span></span></h5>
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