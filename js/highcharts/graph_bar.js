var cnBarGraph = function(options) {
  this.chart = null;
  this.options= options;

  this.tooltip = {
    formatter: function() {
      return ''+
      this.x +': ('+ this.y +')';
    }
  }

  this.plotOptions = {
    column: {
      pointPadding: 0.2,
      borderWidth: 0
    }
  }

  this.yAxis = {
    min: 0,
    lineWidth: 1,
    title: {
      text: ''
    }
  }

  this.legend = {
    layout: 'vertical',
    backgroundColor: '#FFFFFF',
    align: 'left',
    verticalAlign: 'top',
    x: 100,
    y: 70,
    enabled: false
  }

  this.xAxis = {
    lineWidth: 1,
    categories: []
  }

  this.render = function(){
    var seriesData = [];
    var self = this;

    if(self.options.data == 'undefined') {
      $.getJSON(self.options.data_route, function(data) {
        $.each(data, function(label, value) {
          self.xAxis.categories.push(label);
          seriesData.push(value);
        });

        self.showGraph.call(self, seriesData);
      });
    } else {
      $.each(self.options.data, function(label, value) {
        self.xAxis.categories.push(label);
        seriesData.push(value);
      });

      self.showGraph.call(self, seriesData);
    }
  }

  this.showGraph = function(data){
    this.chart = new Highcharts.Chart({
      chart: {
        renderTo: this.options.container,
        margin: [(this.options.title != '' ? 40 : 0), 30, 30, 50]
      },
      title: {
        text: (this.options.title ? this.options.title : '')
      },
      tooltip: this.tooltip,
      plotOptions: this.plotOptions,
      legend: this.legend,
      yAxis: this.yAxis,
      xAxis: this.xAxis,
      exporting: {
        enabled: false
      },
      series: [{
        type: 'column',
        color: '#5f97f7',
        data: data
      }]
    });
  }
}