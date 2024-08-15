"use strict";

var ctx = document.getElementById("comparison-chart").getContext('2d');
var chart_color = ctx.createLinearGradient(0, 0, 0, 120);
chart_color.addColorStop(0, 'rgb(106, 0, 91,.5)');
chart_color.addColorStop(1,'rgb(13, 139, 241,.3)');
var chart2_color = ctx.createLinearGradient(0, 0, 0, 120);
chart2_color.addColorStop(0, 'rgba(255, 78, 0,.8)');
chart2_color.addColorStop(1, 'rgba(255, 78, 0,.3)');


var myChart = new Chart(ctx, {
    data: {
        labels: comparison_chart_labels,
        datasets: [{
            type: 'line',
            label: comparison_chart_year,
            data: comparison_chart_data1,
            borderWidth: 1,
            backgroundColor: chart_color,
            borderWidth: 0,
            borderColor: 'rgba(106, 0, 91,1)',
            pointBorderWidth: 0,
            pointRadius: 0,
            pointBackgroundColor: 'transparent',
            pointHoverBackgroundColor: 'var(--bs-blue)',
        },
        {        
            type: 'line',
            label: comparison_chart_lastyear,
            data: comparison_chart_data2,
            borderWidth: 1,
            backgroundColor: chart2_color,
            borderWidth: 0,
            borderColor: 'rgba(255, 78, 0,1)',
            pointBorderWidth: 0 ,
            pointRadius: 0,
            pointBackgroundColor: 'transparent',
            pointHoverBackgroundColor: '#d1cede',
        }]
    },
    options: {
        legend: {
            display: true
        },
        scales: {
            yAxes: [{
                gridLines: {
                    display: false,
                    drawBorder: true,
                    color: '#f2f2f2',
                },
                ticks: {
                    display: false
                }
            }],
            xAxes: [{
                gridLines: {
                    display: false,
                    tickMarkLength: 15,
                }
            }]
        }
    }
});


var user_chart = document.getElementById("user_chart").getContext("2d");
  var purple_orange_gradient = user_chart.createLinearGradient(0, 0, 0, 300);
  purple_orange_gradient.addColorStop(0, 'rgb(13, 139, 241,.5)');
  purple_orange_gradient.addColorStop(1,'rgb(37, 211, 102,.3)');
  var user_chart_bar = new Chart(user_chart, {
    data: {
      labels: user_summary_label,
      datasets: [{
        type: 'bar',
        label: user_locale,
        data: user_summary_data,
        backgroundColor: purple_orange_gradient,
        borderColor:"transparent",
      }]
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
              fontColor: "#686868",
              display:false
            },
          }],
          xAxes: [{
            offset: true,
            ticks: {
              beginAtZero: true,
              fontColor: "#686868",
              stepSize: user_step_size
            },
            gridLines: {
              display: false
            },
            barPercentage: .9
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
