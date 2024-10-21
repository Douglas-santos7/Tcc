<html>
    <script>
        var options = {
        series: [0], // Porcentagem inicial
        chart: {
          height: 200,
          type: 'radialBar',
          offsetY: 0,
          sparkline: {
            enabled: false
          }
        },
        plotOptions: {
          radialBar: {
            startAngle: -90, // Começa o gráfico no topo
            endAngle: 90, // Termina o gráfico no topo
            hollow: {
              margin: 0,
              size: '50%',
              background: '#fff',
            },
            track: {
              background: '#f0f0f0', // Fundo da trilha
              strokeWidth: '70%',
            },
            dataLabels: {
              show: true,
              name: {
                offsetY: -5,
                color: '#333',
                fontSize: '14px'
              },
              value: {
                formatter: function (val) {
                  return Math.min(Math.round(val), 100); // Arredonda e limita o valor a 100%
                },
                color: '#28a745', // Cor do valor
                fontSize: '22px',
                show: true,
              }
            }
          }
        },
        fill: {
          colors: ['#28a745'], // Cor verde
        },
        stroke: {
          lineCap: 'round'
        },
        labels: ['Porcentagem'],
      };

      var chart = new ApexCharts(document.querySelector("#chart"), options);
      chart.render();


      // Gráfico de progresso das metas
      <?php foreach ($result as $meta) { ?>
        var progresso = Math.min(Math.round((<?php echo ($meta['valor_atual'] / $meta['valor_alvo']) * 100; ?>)), 100); // Limita a 100%

        var chartOptions<?php echo $meta['id']; ?> = {
          series: [progresso], // Passa o valor limitado
          chart: {
            height: 200,
            type: 'radialBar',
            offsetY: 0,
            sparkline: {
              enabled: false
            }
          },
          plotOptions: {
            radialBar: {
              startAngle: -90, // Começa o gráfico no topo
              endAngle: 90, // Termina o gráfico no topo
              hollow: {
                size: '50%',
              },
              track: {
                background: '#f0f0f0', // Fundo da trilha
                strokeWidth: '70%',
              },
              dataLabels: {
                show: true,
                name: {
                  offsetY: -5,
                  color: '#333',
                  fontSize: '14px'
                },
                value: {
                  formatter: function (val) {
                    return Math.min(Math.round(val), 100); // Arredonda e limita o valor a 100%
                  },
                  color: '#28a745', // Cor do valor
                  fontSize: '22px',
                  show: true,
                }
              }
            },
          },
          fill: {
            colors: ['#28a745'], // Cor verde para todos os gráficos
          },
          stroke: {
            lineCap: 'round'
          },
          labels: ['Progresso'],
        };

        var chart<?php echo $meta['id']; ?> = new ApexCharts(document.querySelector("#chart-<?php echo $meta['id']; ?>"), chartOptions<?php echo $meta['id']; ?>);
        chart<?php echo $meta['id']; ?>.render();
      <?php } ?>
    </script>
</html>
