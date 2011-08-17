var qaLineGraph = function(options) {
  this.options = options;
  this.chart = null;
  this.tooltip_titles = [];

  this.config = {
    margin: [20, 20, 40, 50],
    renderTo: this.options.container,
    style: {
      position: "absolute"
    }
  }

  this.xAxis = {
    type: "datetime",
    gridLineWidth: 1,
    title: {
      text: ''
    }
  }

  this.yAxis = {
    title: {
      text: ''
    },
    maxZoom: 0.1,
    minorGridLineWidth: 0,
    gridLineWidth: 0,
    labels: {
      formatter: function() {
        if(this.value >= 1000000)
          return this.value / 1000000 +'M';
        else if(this.value >= 1000)
          return this.value / 1000 +'K';

        return this.value;
      }
    }

  }
  
  this.customLabel = {
    items: [{
      html: '',
      style: {
        'left': '100px',
        'top': '100px'
      }
    }]
  };

  this.defaultPlot = {
    series: {
      fillColor: {
        linearGradient: [0, 0, 0, 0.01],
        stops: [
        [0, "#4572A7"],
        [1, "rgba(0,0,0,0)"]
        ]
      },
      marker: {
        enabled: true,
        symbol: 'circle',
        states: {
          hover: {
            enabled: true,
            radius: 3
          }
        }
      }
    }
  }

  this.plotOptions = function(){
    if (this.options.point_callback) {
      var cnGraph = this;
      this.defaultPlot.series.cursor = 'pointer';
      this.defaultPlot.series.point = {
        events: {
          click: function(event) {
            cnGraph.options.point_callback(this, cnGraph);
          }
        }
      }
    }

    return this.defaultPlot;
  }

  this.legend = {
    enabled: false
  }

  this.credits = {
    enabled: false
  }

  this.graphBlankTitle = {
    text: ""
  }

  this.graphDisableExport = {
    enabled: false
  }

  this.tooltip = function(){
    var cnGraph = this;
    var tip = {};
    tip.formatter = function() {
      var txt = (this.point.name) ? this.point.name : this.series.name;
      if(cnGraph.tooltip_titles && typeof cnGraph.tooltip_titles[txt] != 'undefined')
        txt = cnGraph.tooltip_titles[txt];
      return Highcharts.numberFormat(this.y, 0) + " <b>"+ txt +"</b><br/>"+
      Highcharts.dateFormat("%Y-%m-%d", this.x);
    }

    return tip;
  }

  // render function
  this.render = function(){
    $("#" + this.options.container).css({
      "position": "relative",
      width: this.options.width
      });

    var self = this;
    $.getJSON(self.options.data_route, function(response) {
      self.showGraph.call(self, response);
    });
  }

  // show graph
  this.showGraph = function(response){
    var data = [];
    $('.graph-no-data').hide();
    
    if(typeof response.data != 'undefined') {
      if(response.data.length > 0)
        data = response.data;
      else if($('.graph-no-data').length > 0)
        $('.graph-no-data').show();
    }
                
    if(typeof response.tooltip_titles != 'undefined')
      this.tooltip_titles = response.tooltip_titles;

    if(typeof response.y_title != 'undefined')
      this.yAxis.title.text = response.y_title;

    if(typeof response.x_title != 'undefined')
      this.xAxis.title.text = response.x_title;

    // create a detail chart referenced by a global variable
    this.chart = new Highcharts.Chart({
      chart: this.config,
      credits: this.credits,
      xAxis: this.xAxis,
      yAxis: this.yAxis,
      tooltip: this.tooltip(),
      legend: this.legend,
      plotOptions: this.plotOptions(),
      series: data,
      title: this.graphBlankTitle,
      exporting: this.graphDisableExport,
      label: this.customLabel
    });

    if(this.options.bind_event && this.options.bind_element) {
      var cnGraph = this;
      $('#'+this.options.bind_element).bind('click', function(){
        eval(cnGraph.options.bind_event)(cnGraph, cnGraph.options.bind_event_url)
      });
    }
  }

  // redraw chart
  this.redrawChart = function(response){
    for(var i = this.chart.series.length - 1; i >= 0; i--) {
      this.chart.series[i].remove();
    }


    if(typeof response.data != 'undefined' && response.data.length > 0) {
      $('.graph-no-data').hide();
      for(var i=0; i < response.data.length; i++) {
        this.chart.addSeries(response.data[i]);
      }
    } else if($('.graph-no-data').length > 0) {
      $('.graph-no-data').show();
    }
  }
}

function update_line_chart(cnGraph, url){
  doAjax("get", url, null, "json", function(response) {
      if(response) {
        eval(cnGraph).redrawChart(response);
      }
  });
}