@if(sizeof($listedPresenceGroups) <= 0) <div class="card h-100">
  <div class="card-body p-2">
    Belum ada data.
  </div>
  </div>
  @endif
  <div class="row flex-nowrap overflow-scroll">
    @foreach($listedPresenceGroups as $listedPresenceGroup)
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
      <div class="card h-100">
        <div class="card-body p-2">
          <div class="row">
            <div class="numbers">
              <p class="text-sm mb-0 text-uppercase font-weight-bold">Rata-rata Kehadiran {{ $listedPresenceGroup->name }} ({{ date('M') }})</p>
              <h5 class="font-weight-bolder">
                {{ number_format((float)$listedPresenceGroup->summary_in_month(date('m'), date('Y'))['avg_present_percentage'], 2, '.', '') }}%
              </h5>
              <p class="mb-0">
                <span class="{{ $listedPresenceGroup->summary_in_month(date('m'), date('Y'))['difference_with_previous_month'] > 0 ? 'text-success' : 'text-danger' }} text-sm font-weight-bolder">{{ $listedPresenceGroup->summary_in_month(date('m'), date('Y'))['difference_with_previous_month'] > 0 ? '+' : '' }}{{ number_format((float)$listedPresenceGroup->summary_in_month(date('m'), date('Y'))['difference_with_previous_month'], 2, '.', '') }}%</span>
                dibanding bulan lalu
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endforeach

    @foreach($listedPresenceGroups as $listedPresenceGroup)
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
      <div class="card h-100">
        <div class="card-body p-2">
          <div class="row">
            <div class="numbers">
              <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Presensi {{ $listedPresenceGroup->name }} ({{ date('M') }})</p>
              <h5 class="font-weight-bolder">
                {{ $listedPresenceGroup->summary_in_month(date('m'), date('Y'))['total_presences'] }}
              </h5>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endforeach
  </div>

  @foreach($listedPresenceGroups as $index => $presenceGroup)
  <?php $differenceWithPreviousYear = $presenceGroup->summary_in_year(date('Y'))['difference_with_previous_year'] ?>
  @if($index == 0 || ($index+1) % 3 == 0)
  <div class="row mt-4">
    @endif
    <div class="col-lg-6 mb-lg-0 mb-4">
      <div class="card z-index-2 h-100">
        <div class="card-header pb-0 pt-3 bg-transparent">
          <h6 class="text-capitalize">Rata-rata Kehadiran {{ $presenceGroup->name }} Sepanjang Tahun (dalam persen)</h6>
          <p class="text-sm mb-0">
            <i class="{{ $differenceWithPreviousYear > 0 ? 'text-success fa fa-arrow-up' : 'text-danger' }}"></i>
            <span class="{{ $differenceWithPreviousYear > 0 ? 'text-success' : 'text-danger' }} text-sm font-weight-bolder">{{ $differenceWithPreviousYear > 0 ? '+' : '' }}{{ number_format((float)$differenceWithPreviousYear, 2, '.', '') }}%</span>
            dibanding tahun lalu
          </p>
        </div>
        <div class="card-body p-2">
          <div class="chart">
            <canvas id="chart-line-year-avg-{{ $presenceGroup->id }}" class="chart-canvas" height="300"></canvas>
          </div>
        </div>
      </div>
    </div>
    @if(($index+1) % 2 == 0 || !isset($listedPresenceGroups[$index+1]))
  </div>
  @endif
  @endforeach

  @foreach($listedPresenceGroups as $index => $presenceGroup)
  @if($index == 0 || ($index+1) % 3 == 0)
  <div class="row mt-4">
    @endif
    <div class="col-lg-6 mb-lg-0 mb-4">
      <div class="card z-index-2 h-100">
        <div class="card-header pb-0 pt-3 bg-transparent">
          <h6 class="text-capitalize">Kehadiran {{ $presenceGroup->name }} Sepanjang Bulan</h6>
        </div>
        <div class="card-body p-2">
          <div class="chart">
            <canvas id="chart-line-month-count-{{ $presenceGroup->id }}" class="chart-canvas" height="300"></canvas>
          </div>
        </div>
      </div>
    </div>
    @if(($index+1) % 2 == 0)
  </div>
  @endif
  @endforeach

  <script>
    try {
      $(document).ready();
    } catch (e) {
      window.location.replace(`{{ url("/") }}`)
    }
  </script>

  <!--   Core JS Files   -->
  <script src="{{ asset('js/plugins/chartjs.min.js') }}"></script>
  <script>
    @foreach($listedPresenceGroups as $presenceGroup)
    var ctx_year_avg_ {
      {
        $presenceGroup - > id
      }
    } = document.getElementById("chart-line-year-avg-{{ $presenceGroup->id }}").getContext("2d");

    var gradientStroke_year_avg_ {
      {
        $presenceGroup - > id
      }
    } = ctx_year_avg_ {
      {
        $presenceGroup - > id
      }
    }.createLinearGradient(0, 230, 0, 50);

    gradientStroke_year_avg_ {
      {
        $presenceGroup - > id
      }
    }.addColorStop(1, 'rgba(94, 114, 228, 0.2)');
    gradientStroke_year_avg_ {
      {
        $presenceGroup - > id
      }
    }.addColorStop(0.2, 'rgba(94, 114, 228, 0.0)');
    gradientStroke_year_avg_ {
      {
        $presenceGroup - > id
      }
    }.addColorStop(0, 'rgba(94, 114, 228, 0)');

    new Chart(ctx_year_avg_ {
      {
        $presenceGroup - > id
      }
    }, {
      type: "line",
      data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        datasets: [{
          label: "Rata-rata kehadiran",
          tension: 0.4,
          borderWidth: 0,
          pointRadius: 0,
          borderColor: "#5e72e4",
          backgroundColor: gradientStroke_year_avg_ {
            {
              $presenceGroup - > id
            }
          },
          borderWidth: 3,
          fill: true,
          data: {
            {
              json_encode($presenceGroup - > summary_in_year(date('Y'))['avg_present_percentage_monthly'])
            }
          },
          maxBarThickness: 6

        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          }
        },
        interaction: {
          intersect: false,
          mode: 'index',
        },
        scales: {
          y: {
            grid: {
              drawBorder: false,
              display: true,
              drawOnChartArea: true,
              drawTicks: false,
              borderDash: [5, 5]
            },
            ticks: {
              display: true,
              padding: 10,
              color: '#fbfbfb',
              font: {
                size: 11,
                family: "Titillium Web",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
          x: {
            grid: {
              drawBorder: false,
              display: false,
              drawOnChartArea: false,
              drawTicks: false,
              borderDash: [5, 5]
            },
            ticks: {
              display: true,
              color: '#ccc',
              padding: 20,
              font: {
                size: 11,
                family: "Titillium Web",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
        },
      },
    });
    @endforeach

    @foreach($listedPresenceGroups as $presenceGroup)
    var ctx_month_count_ {
      {
        $presenceGroup - > id
      }
    } = document.getElementById("chart-line-month-count-{{ $presenceGroup->id }}").getContext("2d");

    var gradientStroke_month_count_ {
      {
        $presenceGroup - > id
      }
    } = ctx_month_count_ {
      {
        $presenceGroup - > id
      }
    }.createLinearGradient(0, 230, 0, 50);

    gradientStroke_month_count_ {
      {
        $presenceGroup - > id
      }
    }.addColorStop(1, 'rgba(94, 114, 228, 0.2)');
    gradientStroke_month_count_ {
      {
        $presenceGroup - > id
      }
    }.addColorStop(0.2, 'rgba(94, 114, 228, 0.0)');
    gradientStroke_month_count_ {
      {
        $presenceGroup - > id
      }
    }.addColorStop(0, 'rgba(94, 114, 228, 0)');

    new Chart(ctx_month_count_ {
      {
        $presenceGroup - > id
      }
    }, {
      type: "line",
      data: {
        labels: <?php echo ($presenceGroup->summary_in_month(date('m'), date('Y'))['presences']->map(function ($v) {
                  return $v->name;
                })) ?>,
        datasets: [{
          label: "Jumlah kehadiran",
          tension: 0.4,
          borderWidth: 0,
          pointRadius: 0,
          borderColor: "#5e72e4",
          backgroundColor: gradientStroke_month_count_ {
            {
              $presenceGroup - > id
            }
          },
          borderWidth: 3,
          fill: true,
          data: <?php echo ($presenceGroup->summary_in_month(date('m'), date('Y'))['presences']->map(function ($v) {
                  return $v->summary()['total'];
                })) ?>,
          maxBarThickness: 6

        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          }
        },
        interaction: {
          intersect: false,
          mode: 'index',
        },
        scales: {
          y: {
            grid: {
              drawBorder: false,
              display: true,
              drawOnChartArea: true,
              drawTicks: false,
              borderDash: [5, 5]
            },
            ticks: {
              display: true,
              padding: 10,
              color: '#fbfbfb',
              font: {
                size: 11,
                family: "Titillium Web",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
          x: {
            grid: {
              drawBorder: false,
              display: false,
              drawOnChartArea: false,
              drawTicks: false,
              borderDash: [5, 5]
            },
            ticks: {
              display: true,
              color: '#ccc',
              padding: 20,
              font: {
                size: 11,
                family: "Titillium Web",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
        },
      },
    });
    @endforeach
  </script>