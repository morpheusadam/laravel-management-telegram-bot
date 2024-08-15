"use strict";

var subscriber_summary = document.getElementById("subscriber_summary").getContext('2d');
var subscriber_summary_color = subscriber_summary.createLinearGradient(0, 0, 0, 150);
subscriber_summary_color.addColorStop(0, 'rgb(106, 0, 91,.5)');
subscriber_summary_color.addColorStop(1,'rgb(13, 139, 241,.5)');

var myChart = new Chart(subscriber_summary, {
  type: 'line',
  data: {
    labels: subscriber_gain_data_month_names,
    datasets: [{
      label: local_subscribers,
      data: total_subscriber_gain_data_month_data,
      backgroundColor: subscriber_summary_color,
      borderWidth: 1,
      borderColor: 'rgb(106, 0, 91,.2)',
      pointBorderWidth: 0,
      pointBorderColor: 'transparent',
      pointRadius: 3,
      pointBackgroundColor: 'transparent',
      pointHoverBackgroundColor: 'rgba(63,82,227,1)',
    }]
  },
  options: {
    layout: {
      padding: {
        bottom: -10,
        left: -10
      }
    },
    legend: {
      display: false
    },
    scales: {
      yAxes: [{
        gridLines: {
          display: false,
          drawBorder: false,
        },
        ticks: {
          beginAtZero: true,
          display: false
        }
      }],
      xAxes: [{
        gridLines: {
          drawBorder: false,
          display: false,
        },
        ticks: {
          display: false
        }
      }]
    },
  }
});



var broadcast_summary = document.getElementById("broadcast_summary").getContext('2d');
var broadcast_summary_color = broadcast_summary.createLinearGradient(0, 0, 0, 90);
broadcast_summary_color.addColorStop(0, 'rgb(106, 0, 91,.5)');
broadcast_summary_color.addColorStop(1,'rgb(13, 139, 241,.5)');

var myChart = new Chart(broadcast_summary, {
  type: 'line',
  data: {
    labels: broadcast_summary_days_data,
    datasets: [{
      label: '',
      data: broadcast_summary_data,
      backgroundColor: broadcast_summary_color,
      borderWidth: 1.5,
      borderColor: 'rgb(106, 0, 91,.2)',
      pointBorderWidth: 0,
      pointBorderColor: 'transparent',
      pointRadius: 3,
      pointBackgroundColor: 'transparent',
      pointHoverBackgroundColor: 'rgb(106, 0, 91,.2)'
    }]
  },
  options: {
    layout: {
      padding: {
        bottom: 20,
        left: -10
      }
    },
    legend: {
      display: false
    },
    scales: {
      yAxes: [{
        gridLines: {
          display: false,
          drawBorder: false,
        },
        ticks: {
          beginAtZero: true,
          display: false
        }
      }],
      xAxes: [{
        gridLines: {
          drawBorder: false,
          display: false,
        },
        ticks: {
          display: false
        }
      }]
    },
  }
});

var monthly_subscriber_gain_chart = document.getElementById("monthly_subscriber_years").getContext("2d");
var chart_color1 = monthly_subscriber_gain_chart.createLinearGradient(0, 0, 0, 120);
chart_color1.addColorStop(0, 'rgb(106, 0, 91,.5)');
chart_color1.addColorStop(1,'rgb(13, 139, 241,.3)');

var chart_color2 = monthly_subscriber_gain_chart.createLinearGradient(0, 0, 0, 120);
chart_color2.addColorStop(0, 'rgb(7, 94, 84.5)');
chart_color2.addColorStop(1,'rgb(37, 211, 102,.3)');
var monthly_subscriber_gain_chart_bar = new Chart(monthly_subscriber_gain_chart, {
  data: {
    labels: subscriber_gain_data_month_names,
    datasets: [{
      type: 'line',
      label: local_subscribers_this_year,
      data: subscriber_gain_data_month_data,
      borderColor: '#0D8BF1',
      backgroundColor: chart_color1,
      pointBackgroundColor: '#0D8BF1',
      borderWidth:1,
      pointRadius: 2,
      pointHoverRadius: 2
    },]
  },
  options: {
    responsive: true,
      maintainAspectRatio: true,
      scales: {
        yAxes: [{
          gridLines: {
            drawBorder: false,
            display: false
          },
          ticks: {
            beginAtZero: true,      
            fontColor: "#686868"
          },
        }],
        xAxes: [{
          offset: true,
          ticks: {
            beginAtZero: true,
            fontColor: "#686868",
            stepSize: step_size
          },
          gridLines: {
            display: false
          },
          barPercentage: 0.5
        }]
      },
      legend: {
        display: false,
        position: 'bottom'
      },
      elements: {
        point: {
          radius: 2
        }
      }
}
});

var unitlist = ["","K","M","B","T"];
function num_format(number) {
    let sign = Math.sign(number);
    let unit = 0;
    
    while(Math.abs(number) >= 1000)
    {
      unit = unit + 1; 
      number = (Math.abs(number) / 1000).toFixed(2);
    }

    return sign*Math.abs(number) + unitlist[unit];
}

var monthly_subscriber_years_pie = $("#monthly_subscriber_years_pie").get(0).getContext("2d");
var monthly_subscriber_years_pie_chart = new Chart(monthly_subscriber_years_pie, {
  type: 'pie',
  data: {
    datasets: [{
      data: [subscriber_count_yearly],
      backgroundColor: [
        '#0D8BF1',
        '#25D366'
      ],
      borderColor: [
        '#0D8BF1',
        '#25D366'
      ],
    }],

    // These labels appear in the legend and in the tooltips when hovering different arcs
    labels: [
      current_year_name_telegram,
    ]
  },
  options: {
    cutoutPercentage: 75,
    responsive: true,
    animation: {
      animateScale: true,
      animateRotate: true
    },
    legend: {
      display: false
    },
    legendCallback: function(chart) { 
      var text = [];
      text.push('<ul class="list-group">');
      for (var i = 0; i < chart.data.datasets[0].data.length; i++) {
        text.push('<li class="list-group-item border-0 text-sm"><i class="fas fa-circle" style="color:' + chart.data.datasets[0].backgroundColor[i] + '"></i> ');
        if (chart.data.labels[i]) {
          text.push(chart.data.labels[i]);
        }
        text.push('<label class="badge rounded float-end" style="background:'+chart.data.datasets[0].backgroundColor[i]+'">'+ num_format(chart.data.datasets[0].data[i]) + '</label>');
        text.push('</li>');
      }
      text.push('</ul>');
      return text.join("");
    }
  }
});
document.getElementById('monthly_subscriber_years_pie_legend').innerHTML = monthly_subscriber_years_pie_chart.generateLegend();


var radialBarsOptions = {
  series: [current_month_subscriber_percentage, previous_month_subscriber_percentage],
  chart: {
    height: 300,
    type: "radialBar",
  },
  theme: {
    mode: "light",
    palette: "palette7",
    monochrome: {
      enabled: true
    },
  },
  plotOptions: {
    radialBar: {
      dataLabels: {
        name: {
          offsetY: -15,
          fontSize: "22px",
        },
        value: {
          fontSize: "2.5rem",
        },
        total: {
          show: true,
          label: local_subscribers,
          color: "#0D8BF1",
          fontSize: "16px",
          formatter: function(w) {
            // By default this function returns the average of all series. The below is just an example to show the use of custom formatter function
            return current_month_subscriber;
          },
        },
      },
    },
  },
  labels: [current_month_name, previous_month_name],
};
var radialBars = new ApexCharts(document.querySelector("#radialBars"), radialBarsOptions);
radialBars.render();

$(document).on('click', '.onchange_action .dropdown-item', function(event) {
    event.preventDefault();
    var month = '';
    var year = '';
    var currency = '';

    var id = $(this).parent().parent().attr('id');
    if(id=='change_month') month = $(this).attr('data-item');
    if(id=='change_year') year = $(this).attr('data-item');
    if(id=='change_currency') currency = $(this).attr('data-item');

    $.ajax({
          url: dashboard_change_data,
          type: 'POST',
          data: {month,year,currency},
          beforeSend: function (xhr) {
              xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
          },
          success:function(response)
          {
             location.reload();
          }
      })


});