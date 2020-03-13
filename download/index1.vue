<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
  <title>环保</title>
  <link rel="stylesheet" type="text/css" href="./static/govern/css/society.css" />
  <link rel="stylesheet" type="text/css" href="./static/hb/css/hb.css" />
  <link href="//netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
  <link href="./static/css/ol.css" />
  <script src="./static/js/axios.min.js"></script>
  <script src="./static/js/echart.js" type="text/javascript" charset="utf-8"></script>
  <script src="./static/js/jquery-3.1.1.min.js" type="text/javascript" charset="utf-8"></script>
  <script src="./static/js/vue.min.js" type="text/javascript" charset="utf-8"></script>
  <script src="./static/js/component/menu/menu.js"></script>
  <script src="./static/js/component/echart/hcechart.js"></script>
  <script src="./static/js/component/echart/zyechart.js"></script>
  <script src="./static/js/component/echart/qyechart.js"></script>
  <script src="./static/js/component/echart/zdechart.js"></script>
  <script src="./static/js/component/echart/yzechart.js"></script>
  <script src="./static/js/component/echart/ylechart.js"></script>
  <script src="./static/js/component/echart/fpechart.js"></script>
  <script src="./static/js/component/echart/rkechart.js"></script>
  <script src="./static/js/common.js"></script>
  <script src="./static/js/ol.js"></script>

  <script src="./static/js/request.js"></script>
  <script type="text/javascript" src="./static/openlayers/include-openlayers.js"></script>
  <!-- <script src="./static/hb/js/hb.js"></script> -->
</head>

<body>
  <div id="app">
    <div class="title">
      <img src="./static/wg/image/jiantou.png" class="reture" onclick="window.location.href='localHome.html'" style="
				width: 24px;
				height: 24px;
				font-size: 25px;
				font-weight: normal;
				position: absolute;
				left: 10px;
				top: 14px;
				z-index: 999;" />
      <p>环保平台</p>
      <div class="h-scroll">
        <div class="village" @click="show=true" v-text="area"></div>
        <div class="uni-slidingMenu" scroll-x style="background:#777777">
          <div class="slidingMenuList" v-for="(item, index) in list" :key="index" @click="getActive(item)" v-cloak>
            {{ item.name }}
            <div :class="['slidingMenuList-bor',activeIndex==item.id?'active':'']"></div>
          </div>
        </div>
      </div>
    </div>
    <!-- <div class="other">
      <div class="Menu">
        <div class="slidingMenuList" v-for="(item, index) in list1" :key="index" @click="getActive1(item)" v-cloak>
          {{ item.name }}
          <div :class="['slidingMenuList-bor',activeIndex1==item.id?'active':'']"></div>
        </div>
      </div>
    </div> -->
    <div class="black" v-show='show' @click="black">
      <div class="town-list">
        <select class="xl" v-model="selects" @change="getselectss(selects)" style="text-align: center;
                text-align-last: center;
                font-size: 16px;
                background: #2267b5;
                border: none;
                color: rgb(164, 204, 248);">
          <option value="站点">站点</option>
          <option value="网格">网格</option>
        </select>
        <div v-for="(item, index) in areaList" :key='index' :class="['town', townIndex == item.id ? 'town-active' : '']"
          @click="town(item)" v-text="item.pointName">
        </div>
      </div>
      <div class="village-list" v-show='isShowVillage'>
        <div v-for="(item, vindex) in villageList" :key='vindex'
          :class="['Village', villageIndex == item.id ? 'village-active' : '']" @click="village(item)"
          v-text="item.name"></div>
      </div>
    </div>
    <div id="map" style="width:100%; height:calc(100vh); background-color: #012869;"></div>
    <div class="echar-btn" @click="open" v-show='hideit' style="display: none;"><img src="./static/images/look.png"
        alt=""></div>
    <div class="fd"><img @click="zoomIn()" src="./static/images/up.png" alt=""><img @click="zoomOut()"
        src="./static/images/down.png" alt=""></div>
    <div :class="['address', showit == true ? 'address1' : '', showAll == true ? 'address2' : '']"
      @click='initAddress()'><img src="./static/images/address.png" alt=""></div>
    <!-- 站点弹窗 -->
    <div class="table" v-show="showtable" style="display: none;">
      <img src="./static/images/xhao_03.png" @click="closeLaery" mode=""></img>
      <div class="xtable">
        <div class="map-markerPopup-top">
          <span class="iconfont" v-text="tu_list.pointName"></span>
          <span>(在线)</span>
        </div>
        <div class="map-markerPopup-time">监测时间:{{tu_list.monitorTime}}</div>
        <div class="map-markerPopup-center">
          <span class="map-markerPopup-AQI">
            <i>AQI</i>
            <i style="color: #8E1552;" v-text="tu_list.aqi"></i>
          </span>
          <span class="map-markerPopup-grade">
            <i>重度</i>
            <i style="color: #8E1552;" v-text="tu_list.lev_zhong"></i>
          </span>
          <span class="map-markerPopup-first">
            <i>首要污染物</i>
            <i v-text="tu_list.firstPollutant"></i>
          </span>
        </div>
        <div class="markerPopup-center-bottom">
          <span><i>PM2.5&nbsp;&nbsp;</i><i style="color: #FF465C" v-text="tu_list.pm25"></i></span>
          <span><i>PM10&nbsp;&nbsp;</i><i style="color: #FB9726" v-text="tu_list.pm10"></i></span>
          <span><i>SO2&nbsp;&nbsp;</i><i style="color: #08C889" v-text="tu_list.so2"></i></span>
          <span><i>CO&nbsp;&nbsp;</i><i style="color: #08C889" v-text="tu_list.co"></i></span>
          <span><i>NO2&nbsp;&nbsp;</i><i style="color: #08C889" v-text="tu_list.no2"></i></span>
          <span><i>O3&nbsp;&nbsp;</i><i style="color: #08C889" v-text="tu_list.o3"></i></span>
          <!-- <div class="markerPopup-center-air"> -->
          <span><i>温度</i><i v-text="tu_list.tem"></i></span>
          <span><i>湿度</i><i v-text="tu_list.rh"></i></span>
          <span><i>风向</i><i v-text="tu_list.wd"></i></span>
          <span><i>风速</i><i v-text="tu_list.ws"></i></span>
          <span><i>压力</i><i v-text="tu_list.pa"></i></span>
          <!-- </div> -->
          <!--<div class="markerPopup-center-BtnBox">
            <b class="iconfont" onclick="vm.historyBtn()">历史数据</b>
          </div>-->
        </div>
      </div>
    </div>
    <!-- 网格弹窗 -->
    <div class="table" v-show="showtable1" style="display: none;">
      <img src="./static/images/xhao_03.png" @click="closeLaery" mode=""></img>
      <div class="xtable">
        <div class="map-markerPopup-top">
          <span class="iconfont" v-text="tu_list.groupName"></span>
        </div>
        <div class="map-markerPopup-time">监测时间:{{tu_list.monitorTime}}</div>
        <div class="map-markerPopup-center">
          <span class="map-markerPopup-AQI">
            <i>AQI</i>
            <i style="color: #8E1552;" v-text="tu_list.aqi"></i>
          </span>
          <span class="map-markerPopup-grade">
            <i>重度</i>
            <i style="color: #8E1552;" v-text=""></i>
          </span>
        </div>
        <div class="markerPopup-center-bottom">
          <span><i>SO2&nbsp;&nbsp;</i><i style="color: #08C889" v-text="tu_list.so2"></i></span>
          <span><i>PM2.5&nbsp;&nbsp;</i><i style="color: #FF465C" v-text="tu_list.pm25"></i></span>
          <span><i>CO&nbsp;&nbsp;</i><i style="color: #08C889" v-text="tu_list.co"></i></span>
          <span><i>NO2&nbsp;&nbsp;</i><i style="color: #08C889" v-text="tu_list.no2"></i></span>
          <span><i>PM10&nbsp;&nbsp;</i><i style="color: #FB9726" v-text="tu_list.pm10"></i></span>
          <span><i>O3&nbsp;&nbsp;</i><i style="color: #08C889" v-text="tu_list.o3"></i></span>
        </div>
      </div>
    </div>
  </div>
</body>

</html>


<style>
  .title p {
    text-align: center;
    color: #fff;
    padding: 5px 0px;
    font-size: 18px;
  }

  .other {
    top: 96px;
  }

  .fd {
    top: 120px;
  }

  .title {
    /* background-color: #2267b5 !important; */
  }

  .xtable ul li {
    border-bottom: 1px solid #355d93;
    color: #fff;
    height: 30px;
    background: #023478;
  }

  .xtable .label {
    text-align: right;
  }

  .table img {
    position: absolute;
    top: -5px;
    right: -3px;
    width: 25px;
    height: 25px;
    text-align: right;
  }

  .uni-slidingMenu {
    width: 100%;
    white-space: nowrap;
    height: auto;
    overflow-x: auto;
    /* background-color: #00A0EA !important; */
  }

  .slidingMenuList {
    line-height: 30px;
    display: inline-block;
    font-size: 16px;
    padding: 0 10px;
    height: 32px;
    color: #fff;
  }

  .active {
    height: 2px;
  }

  .slidingMenuList-bor,
  .slidingMenuList-con {
    width: 24px;
    background: #fff !important;
    margin: auto;
    border-radius: 10px;
  }

  .slidingMenuList-con {
    background: #fff;
  }

  .echar {
    height: 215px;
    margin-bottom: 10px;
    /* margin-top: -215px; */
    /* background: plum; */
    /* position: absolute; */
    width: 354px;
    margin: 0 auto;
    /* left: 10px; */
    /* bottom: 58px; */
    border-radius: 10px;
    background: #fff;
  }

  .canvasdiv {
    height: 100%;
  }

  .ec-canvas {
    display: flex;
    height: 100%;
    flex: 1;
  }

  .table {
    position: absolute;
    left: 0;
    right: 0;
    width: auto;
    margin: 0 auto;
    /* left: 14px; */
    bottom: 58px;
    height: 220px;
    background: #023478;
  }

  .table table {
    border-collapse: collapse;
    border-spacing: 0;
    width: 90%;
    margin: 0 auto;
  }

  .table th,
  .table td {
    border-bottom: 1px solid #355d93;
    color: #fff;
    height: 30px;
    background: #023478;
    padding-left: 11px;
  }

  .table tr td:first-child {
    text-align: right;
  }

  /* .xtable {
    margin-top: 5px;
    overflow: auto;
    width: 100%;
    height: 180px;
  } */

  /* .tab {
		display: flex;
		width: 100%;
	}
	.tab .label {
		justify-content: space-between;
	} */
</style>
<script>

  var app = new Vue({
    el: "#app",
    data() {
      return {
        userType: '',
        show: false,
        showAll: false,
        showit: false,
        hideit: false,
        showtable: false,
        showtable1: false,
        userhz: {},
        userot: {},
        shuijing: {},

        list: [
          {
            "id": "wxz",
            "name": "微型站",
            "map": "wxz",
            "child": []
          },
          {
            "id": "xzx",
            "name": "小型站",
            "map": "xzx",
            "child": []
          },
          {
            "id": "skz",
            "name": "省控站",
            "map": "xzx",
            "child": []
          },
          {
            "id": "xzz",
            "name": "乡镇站",
            "map": "xzz",
            "child": []
          },
          {
            "id": "tovc",
            "name": "TVOC站",
            "map": "tovc",
            "child": []
          },
          {
            "id": "jcwg",
            "name": "基础网格",
            "map": "jcwg",
            "child": []
          }
        ],
        list1: [
          {
            "id": "aqi",
            "name": "AQI",
            "map": "aqi",
            "child": []
          },
          {
            "id": "pm2.5",
            "name": "PM2.5",
            "map": "pm",
            "child": []
          },
          {
            "id": "pm10",
            "name": "PM10",
            "map": "xzx",
            "child": []
          },
          {
            "id": "so",
            "name": "SO2",
            "map": "so",
            "child": []
          },
          {
            "id": "co",
            "name": "CO",
            "map": "co",
            "child": []
          },
          {
            "id": "no",
            "name": "NO2",
            "map": "no",
            "child": []
          },
          {
            "id": "o3",
            "name": "O3",
            "map": "o3",
            "child": []
          }
        ],
        color: '',
        color1: 'rgba(55, 182, 255,0.8)',
        color2: '',
        townIndex: ' ',
        villageIndex: ' ',
        layers: {},
        map: {},
        area: {},
        areaList: [],
        villageList: [
          {
            name: 1,
          },
          {
            name: 6
          },
          {
            name: 3,
          }
        ],
        isShowVillage: false,
        activeIndex: 'wxz', ///菜单栏选中状态的index值
        activeIndex1: 'aqi',
        selects: '站点',
        stype: 1,
        SList: [],
        pointList: [],
        siteStyle: [],

        n: 0,
        mapLayer: [],
        point: [],
        info: {},
        datas: [],
        p1id: 1,
        p2id: 1,
        layer_list: [],
        tu_list: {},
        ppid: []


      }
    },
    mounted() {
      this.bindmap();
      this.getVillage();
      this.selectss();
      this.getMapInfo();
      this.getMapDian();



    },
    methods: {

      //页面加载获取站点/镇
      selectss() {
        let that = this;
        getRequest('/Huanbao/getSelects', {
          id: getUrlParam('id'),
          stype: this.stype,
        }, function (res) {
          if (res.code == 200) {
            that.areaList = res.data;

            that.area = res.data[0].pointName;
          }
        }, function (errcode, errmsg) {
          alert('code ' + errcode + ' ,meaasge ' + errmsg)
        });
      },

      //站点选择
      getselectss(e) {
        let that = this;
        if (e == '站点') {
          that.stype = 1;
          that.selectss();
          //线
          that.color1 = 'rgba(55, 182, 255,0.8)';
          that.getMapInfo();

        } else if (e == '网格') {
          that.stype = 2;
          that.selectss();
          that.color1 = 'rgba(251, 151, 38,1.0)';
          that.getMapInfo();
        }
      },

      //根据点位生成多边形
      createPolygon() {
        this.SList = [];
        let c = 0;

        for (let i in this.pointList) {
          let _points = this.pointList[i]; //点位

          for (let j in _points) {
            this.SList.push(new ol.source.Vector({ wrapX: true })); //创建用于绘制形状的图层
            var vector = new ol.layer.Vector({
              source: this.SList[c],
              style: this.siteStyle[i]
            }); //设置图层样式

            this.map.addLayer(vector); //将图层添加至地图
            this.mapLayer.push(vector);
            var geometry = new ol.geom.Polygon([_points[j]]); //获取点位对象
            // console.log(_points);
            var feature = new ol.Feature({
              geometry: geometry
            }); //点位转换为图形对象
            this.SList[c].addFeatures([feature]); //将图形添加至目标图层
            c++;
          }
        }
      },
      getMapInfo() {
        let that = this;
        postRequest('/Huanbao/getMapPoint', {}, function (res) {
          if (res.code == 200) {
            that.n = 0;
            that.pointList = [];
            that.siteStyle = [];
            for (var i = 0; i < res.data.length; i++) {

              that.pointList.push(JSON.parse(res.data[i].lnglat));
              let color = res.data[i].color;
              color = color.replace("rgb(", "");
              color = color.replace(")", "");
              color = color.replace(/ /g, "");
              color = color.split(",");
              that.siteStyle.push(
                new ol.style.Style({
                  fill: new ol.style.Fill({
                    color:
                      "rgba(" +
                      color[0] +
                      "," +
                      color[1] +
                      "," +
                      color[2] +
                      ",0.1)"
                  }),
                  stroke: new ol.style.Stroke({
                    // color:
                    // 	"rgba(" +
                    // 	color[0] +
                    // 	"," +
                    // 	color[1] +
                    // 	"," +
                    // 	color[2] +
                    // 	",0.8)",
                    color: that.color1,
                    width: 1
                  })
                })
              );
            }
            that.createPolygon();
          }
        }, function (errcode, errmsg) {
          alert('code ' + errcode + ' ,meaasge ' + errmsg)
        });
      },

      //获取点位信息
      getMapDian() {
        let that = this;
        // for (let i = 1; i <= 6; i++) {
        // if (that.ppid.indexOf(that.p1id) < 0) {
        postRequest('/Huanbao/getMapDian',
          { p1id: that.p1id }, function (res) {
            if (res.code == 200) {
              that.ppid.push(that.p1id);
              that.datas = res.data;
              // that.addSourceFromUrls(that.);
              that.createMark({
                data: res.data,
                // src: "http://150.242.235.66:3970/icon_GPS.png",
                src: "./static/images/hb-2.png",
                p1id: that.p1id,
                // p2id:that.p2id
              });
              // that.showFirst();
            }
          }, function (errcode, errmsg) {
            alert('code ' + errcode + ' ,meaasge ' + errmsg)
          });
        // }
        // }
      },
      showFirst() {
        for (let i in this.layer_list) {
          if (1 == this.layer_list[i].type) {
            this.layer_list[i].layer.setVisible(true);
          }
        }
      },

      createMark(option) {

        let that = this;
        let Features = [];
        for (let i in option.data) {

          var startMarker = new ol.Feature({
            name: "p1",
            type: "icon",
            data: option.data[i],
            geometry: new ol.geom.Point(option.data[i].point)
          });
          Features.push(startMarker);

        }

        var vectorLayer = new ol.layer.Vector({
          source: new ol.source.Vector({
            features: Features
          }),
          style: new ol.style.Style({
            image: new ol.style.Icon({
              anchor: [0.5, 1],
              src: option.src,
              // size: [50, 20],
              scale: 0.15,


            })
          }),
          visible: true
        });

        this.map.addOverlay(vectorLayer);


        that.layer_list.push({
          type: option.p1id,
          // type2: option.p2id,
          layer: vectorLayer,
          show: true
        });
        this.map.on('click', function (evt) {

          var feature = that.map.forEachFeatureAtPixel(evt.pixel, function (feature, layer) {
            return feature;
          });
          var data = feature.N;
          //data 为 写入的数据
          that.tu_list = data.data.info;
          that.showtable = true;
        })

      },

      openClick(id) {
        let that = this;
        if (id) {

          console.log('openClick', id);
          if (id == 'wxz') {
            that.p1id = 1;
            // for (let i in this.layer) {
            // console.log(this.layer[i],123)

            // }

          } else if (id == 'xzx') {
            that.p1id = 2;
          } else if (id == 'skz') {
            that.p1id = 3;
          } else if (id == 'xzz') {
            that.p1id = 4;
          } else if (id == 'tovc') {
            that.p1id = 5;
          } else if (id == 'jcwg') {
            that.p1id = 6;
          } else {
            that.p1id = 0;
          }
          that.getMapDian();

        }
      },
      //大导航
      getActive(item) {
        if (this.activeIndex == item.id) {
          this.activeIndex = 0;
          item.activeIndex = 0;
        } else {
          this.activeIndex = item.id;
          item.activeIndex = item.id;
        }
        let that = this;
        // console.log(item.activeIndex)
        if (!item.activeIndex) {
          if (item.id == 'wxz' || item.id == 'xzx' || item.id == 'skz' || item.id == 'xzz' || item.id == 'tovc' || item.id == 'jcwg') {
            console.log('关闭');

            for (let i in this.layer_list) {

              if (this.p1id == this.layer_list[i].type) {

                this.layer_list[i].layer.setVisible(false);
              }

            }
          }

        } else {

          if (item.id == 'wxz' || item.id == 'xzx' || item.id == 'skz' || item.id == 'xzz' || item.id == 'tovc' || item.id == 'jcwg') {
            console.log('打开');
            this.openClick(item.id); //打开对应的图层点击事件

            for (let i in this.layer_list) {

              if (this.p1id == this.layer_list[i].type) {
                // console.log(this.layer_list[i].layer);
                this.layer_list[i].layer.setVisible(true);
              } else {
                this.layer_list[i].layer.setVisible(false);
              }

            }
          }

        }


      },

      getActive1(item) {
        if (this.activeIndex1 == item.id) {
          this.activeIndex1 = 0;
          item.activeIndex1 = 0;
        } else {
          this.activeIndex1 = item.id;
          item.activeIndex1 = item.id;
        }
        console.log(item);
      },
      black(item) {
        if (this.isShowVillage) {
          if (item.x > 261) this.show = false;
        } else {
          if (item.x > 132) this.show = false;
        }
      },
      //获取图表信息
      town(item) {

        let that = this;

        that.area = item.pointName;
        getRequest('/Huanbao/gettu', {
          id: item.id,
          stype: that.stype,
        }, function (res) {
          if (res.code == 200) {
            that.tu_list = res.data;
            if (that.stype == 1) {
              that.changeMapView([that.tu_list.pointLongitude, that.tu_list.pointLatitude], 12);
              that.showtable = true;
              that.showtable1 = false;
            } else if (that.stype == 2) {
              that.changeMapView([118.02195019731, 36.989653505722], 11);
              that.showtable = false;
              that.showtable1 = true;
            }
            that.show = false;
          }
        }, function (errcode, errmsg) {
          alert('code ' + errcode + ' ,meaasge ' + errmsg)
        });
      },
      //关闭图表
      closeLaery() {
        this.showAll = false;
        this.showtable = false;
        this.showtable1 = false;
      },
      village(item) {

        this.villageIndex = item.id;
        this.area = item;
        this.getEchartHttp(item.name);
        this.changeMapView([item.longitude, item.latitude], 17)
        this.show = false;
      },


			/**
			 * 根据用户身份获取区域并定位到所属区域
			 */
      getVillage() {
        let that = this;
        postRequest('/base/getArea', {
          id: getUrlParam("sid")
        }, function (res) {
        }, function (errcode, errmsg) {
          console.log('code ' + errcode + ' ,meaasge ' + errmsg)
        })
      },
			/**
			 * @param {array} viewCenter 经纬度
			 * @param {int} viewZoom
			 * 根据经纬度定位
			 */
      changeMapView(viewCenter, viewZoom) {
        var viewOptions = {
          duration: 1000,
          center: viewCenter,
          zoom: viewZoom
        };
        this.map.getView().animate(viewOptions);
      },
      //地图放大
      zoomIn() {
        let view = this.map.getView();
        let zoom = view.getZoom();
        view.setZoom(zoom + 1);
      },
      //地图缩小
      zoomOut() {
        let view = this.map.getView();
        let zoom = view.getZoom();
        view.setZoom(zoom - 1);
      },
      //回到起始位置
      initAddress() {
        this.changeMapView([118.02195019731, 36.989653505722], 11)
      },

      bindmap() {
        let that = this;
        //初始化图层
        that.map = new ol.Map({
          target: 'map',
          renderer: 'canvas',
          controls: ol.control.defaults({
            zoom: 0,
            attribution: false
          }).extend([]),
          logo: false,
          view: new ol.View({
            center: [118.021950197308, 36.9896535057221],
            projection: ol.proj.get("EPSG:4326"),
            // minZoom:7,
            // maxZoom:18,
            zoom: 11
          })
        });
        //
        //创建图层
        let layers = new ol.layer.Tile({
          source: new ol.source.TileSuperMapRest({
            url: 'http://222.175.243.21:45622/iserver/services/map-HT_Map_SL/rest/maps/HT_Map_Blue',
            wrapX: false,
            transparent: true
          })
        });
        that.map.addLayer(layers);
      },
    }

  })

</script>