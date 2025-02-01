<script>
        var ctx = document.getElementById("widgetChart1");
    if (ctx) {
      ctx.height = 130;
      var myChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: <?= $datesJS ?>,
          type: 'line',
          datasets: [{
            data: <?= $userCountsJS?>,
            label: 'Dataset',
            backgroundColor: 'rgba(255,255,255,.1)',
            borderColor: 'rgba(255,255,255,.55)',
          },]
        },
        options: {
          maintainAspectRatio: true,
          legend: {
            display: false
          },
          layout: {
            padding: {
              left: 0,
              right: 0,
              top: 0,
              bottom: 0
            }
          },
          responsive: true,
          scales: {
            xAxes: [{
              gridLines: {
                color: 'transparent',
                zeroLineColor: 'transparent'
              },
              ticks: {
                fontSize: 2,
                fontColor: 'transparent'
              }
            }],
            yAxes: [{
              display: false,
              ticks: {
                display: false,
              }
            }]
          },
          title: {
            display: false,
          },
          elements: {
            line: {
              borderWidth: 0
            },
            point: {
              radius: 0,
              hitRadius: 10,
              hoverRadius: 4
            }
          }
        }
      });
    }
    var ctx = document.getElementById("widgetChart2");
    if (ctx) {
      ctx.height = 130;
      var myChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: <?= $productLabelsJS ?>,
          type: 'line',
          datasets: [{
            data: <?= $productCountsJS ?>,
            label: 'Dataset',
            backgroundColor: 'transparent',
            borderColor: 'rgba(255,255,255,.55)',
          },]
        },
        options: {

          maintainAspectRatio: false,
          legend: {
            display: false
          },
          responsive: true,
          tooltips: {
            mode: 'index',
            titleFontSize: 12,
            titleFontColor: '#000',
            bodyFontColor: '#000',
            backgroundColor: '#fff',
            titleFontFamily: 'Montserrat',
            bodyFontFamily: 'Montserrat',
            cornerRadius: 3,
            intersect: false,
          },
          scales: {
            xAxes: [{
              gridLines: {
                color: 'transparent',
                zeroLineColor: 'transparent'
              },
              ticks: {
                fontSize: 2,
                fontColor: 'transparent'
              }
            }],
            yAxes: [{
              display: false,
              ticks: {
                display: false,
              }
            }]
          },
          title: {
            display: false,
          },
          elements: {
            line: {
              tension: 0.00001,
              borderWidth: 1
            },
            point: {
              radius: 4,
              hitRadius: 10,
              hoverRadius: 4
            }
          }
        }
      });
    }

    var productCounts = {
        standard: <?= $standardCount ?>,
        gold: <?= $goldCount ?>,
        premium: <?= $premiumCount ?>
    };


    var ctx = document.getElementById("recent-rep-chart");
if (ctx) {
    ctx.height = 250;
    var myChart = new Chart(ctx, {
        type: 'bar', 
        data: {
            labels: ['Standard', 'Gold','Premium'], 
            datasets: [{
                label: 'Product Counts',
                backgroundColor: ['#36a2eb', '#ffce56','#00ad5f'], 
                data: [productCounts.standard, productCounts.gold, productCounts.premium] 
            }]
        },
        options: {
            maintainAspectRatio: true,
            legend: {
                display: true 
            },
            responsive: true,
            scales: {
                xAxes: [{
                    gridLines: {
                        drawOnChartArea: true,
                        color: '#f2f2f2'
                    },
                    ticks: {
                        fontFamily: "Poppins",
                        fontSize: 12
                    }
                }],
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        maxTicksLimit: 5,
                        stepSize: 1, 
                        fontFamily: "Poppins",
                        fontSize: 12
                    },
                    gridLines: {
                        display: true,
                        color: '#f2f2f2'
                    }
                }]
            }
        }
    });
}
var verifiedData = <?= $verificationData ?>;

var ctx = document.getElementById("percent-chart");
if (ctx) {
    ctx.height = 280;
    var myChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            datasets: [{
                label: "User Verification Status",
                data: verifiedData,
                backgroundColor: ['#00b5e9', '#fa4251'],
                hoverBackgroundColor: ['#00b5e9', '#fa4251'],
                borderWidth: [0, 0],
                hoverBorderColor: ['transparent', 'transparent']
            }],
            labels: ['Verified', 'Not Verified']
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            cutoutPercentage: 55,
            animation: {
                animateScale: true,
                animateRotate: true
            },
            legend: {
                display: false
            },
            tooltips: {
                titleFontFamily: "Poppins",
                xPadding: 15,
                yPadding: 10,
                caretPadding: 0,
                bodyFontSize: 16
            }
        }
    });
}
</script>