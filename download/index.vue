<template>
  <div class="board-container">
    <div class="board-top">
      <div class="board-tleft">
        <div class="board-card-box">
          <div
            class="board-card box-shadow"
            v-for="(item, index) in list"
            :key="index"
            :style="'background-image:url(' + item.bg + ')'"
            @click="goEvent(item.id)"
          >
            <div class="board-card-icon">
              <img :src="item.icon" />
            </div>
            <div class="board-card-info">
              <p v-text="item.name"></p>
              <h6 v-text="item.number"></h6>
            </div>
          </div>
        </div>
        <div class="glo-sta box-shadow">
          <div class="glo-sta-title">
            <span>全局统计</span>
          </div>
          <el-tabs
            v-model="activeName"
            @tab-click="handleClick"
            style="padding:0 15px;"
          >
            <el-tab-pane label="类型统计" name="first">
              <div class="glo-sta-chart">
                <div id="chart1" style="width:100%;height:180px"></div>
              </div>
            </el-tab-pane>
            <!-- <el-tab-pane label="案件状态" name="second">
              <div class="glo-sta-chart">
                <div id="chart2" style="width:100%;height:180px;"></div>
              </div>
            </el-tab-pane>-->
            <el-tab-pane label="区域统计" name="third">
              <div class="glo-sta-chart">
                <div id="chart3" style="width:710px;height:180px;"></div>
              </div>
            </el-tab-pane>
          </el-tabs>
        </div>
      </div>
      <div class="board-tright">
        <div class="map-box" :class="mapFull ? 'full' : ''">
          <div
            id="map"
            style="width: 100%;height:100%;border:1px solid #C5D0DC;"
          ></div>
          <!-- <div class="map-setting">
            <button @click="mapSet=!mapSet">设置</button>
            <div class="map-setting-box box-shadow" v-show="mapSet">
              <div class="map-setting-item" v-for="item in polygon" :key="item.id">
                <label>
                  <input type="checkbox" checked @change="changePoly($event,item.id)" />
                  <span v-text="item.name"></span>
                </label>
              </div>
            </div>
          </div>-->
          <!-- <div class="map-screen" @click="mapFull=!mapFull">
            <i :class="mapFull ? 'el-icon-zoom-out' : 'el-icon-zoom-in'"></i>
          </div>-->
          <!-- <baidu-map
            class="map"
            :center="{lng: 118.104459, lat: 36.966101}"
            :zoom="15"
            :scroll-wheel-zoom="true"
          >
          </baidu-map>-->
        </div>
      </div>
    </div>
    <div class="board-list">
      <div class="board-list-title">
        <span>事件列表</span>
        <router-link to="/case/robot/list">更多&gt;</router-link>
      </div>
      <div class="board-list-table th-main-table">
        <!-- <el-table :data="tableData" stripe style="width: 100%" height="253" @row-click="goDetails"> -->
        <el-table :data="tableData" stripe style="width: 100%" height="253">
          <el-table-column
            prop="index"
            type="index"
            label="序号"
            width="75"
          ></el-table-column>
          <!-- <el-table-column prop="zfCaseInfoPerson.personname" label="当事人">
            <template slot-scope="scope">
              <div>
                <p
                  v-if="scope.row.zfCaseInfoPerson.personname!=''"
                >{{scope.row.zfCaseInfoPerson.personname}}</p>
                <p
                  v-if="scope.row.zfCaseInfoUnit.unitname!=''"
                >{{scope.row.zfCaseInfoUnit.unitname}}</p>
              </div>
            </template>
          </el-table-column> -->
          <el-table-column prop="sn" label="任务号"></el-table-column>
          <el-table-column prop="wtlx_str" label="问题类型"></el-table-column>
          <el-table-column prop="dlmc_str" label="大类名称"></el-table-column>
          <el-table-column prop="xlmc_str" label="小类名称"></el-table-column>
          <el-table-column
            prop="dzms"
            label="地址描述"
            width="150"
            :show-overflow-tooltip="true"
          ></el-table-column>
          <!-- <el-table-column prop label="部件编码"></el-table-column> -->
          <el-table-column
            prop="wtms"
            label="问题描述"
            :show-overflow-tooltip="true"
          ></el-table-column>
          <el-table-column prop="slsj" label="受理时间"></el-table-column>
          <!-- <el-table-column prop="latj" label="立案条件"></el-table-column> -->
          <!-- <el-table-column prop="jsqy" label="计时区域"></el-table-column> -->
          <el-table-column prop="dqsx" label="当前阶段时限"></el-table-column>
          <el-table-column
            prop="dqjzsj"
            label="当前阶段截止时间"
          ></el-table-column>
          <el-table-column label="操作">
            <template slot-scope="scope">
              <button
                class="t-btn"
                title="详情"
                @click.stop="goDetails(scope.row)"
              >
                <img src="@/assets/btn_detail.png" />
              </button>
            </template>
          </el-table-column>
          <!-- <el-table-column label="操作">
            <template>
              <button
                class="t-btn"
                title="编辑"
              >
                <img src="@/assets/btn_edit.png">
              </button>
            </template>
          </el-table-column>-->
        </el-table>
      </div>
    </div>
    <div>
      <el-dialog
      class="adduser-dialog"
      :title="istitle"
      :visible.sync="dialogVisible"
      width="800px"
      v-dialogDrag
    >
    
      <div>
        <span>任务编号：</span>
        <span v-text="info.sn"></span>
      </div>
      <div>
        <span>问题类型：</span>
        <span v-text="info.wtlx_str"></span>
      </div>
      <div>
        <span>大类：</span>
        <span v-text="info.dlmc_str"></span>
      </div>
      <div>
        <span>小类：</span>
        <span v-text="info.xlmc_str"></span>
      </div>
      <div>
        <span>发生时间：</span>
        <span v-text="info.fssj"></span>
      </div>
      <div>
        <span>当事人：</span>
        <span v-text="info.dsr"></span>
      </div>
      <div>
        <span>联系电话：</span>
        <span v-text="info.lxdh"></span>
      </div>
      <div>
        <span>事件描述：</span>
        <span v-text="info.wtms"></span>
      </div>
    
    </el-dialog>
    </div>
  </div>
</template>
<style>
.adduser-dialog div{
  line-height: 40px;
}
</style>
<script>
import echarts from "echarts";
import { service } from "@/utils/request";

export default {
  name: "Board",
  // mixins:[Mark],
  data() {
    return {
      dialogVisible:false,
      istitle:'案件信息',
      info:[],
      list: [
        {
          id: "1",
          name: "待办",
          number: 0,
          icon: require("@assets/board_list_03.png"),
          bg: require("@assets/board_card_bg_03.png")
        },
        {
          id: "2",
          name: "已办",
          number: 0,
          icon: require("@assets/board_list_05.png"),
          bg: require("@assets/board_card_bg_06.png")
        },
        {
          id: "3",
          name: "超时",
          number: 0,
          icon: require("@assets/board_list_09.png"),
          bg: require("@assets/board_card_bg_11.png")
        },
        {
          id: "4",
          name: "预警",
          number: 0,
          icon: require("@assets/board_list_10.png"),
          bg: require("@assets/board_card_bg_13.png")
        }
      ],
      tableData: [],
      groups: [],
      activeName: "first",
      source: null,
      url: [
        "http://222.175.243.21:45622/iserver/services/map-HT_Map_SL/rest/maps/HT_Map_White"
      ],
      userList: [],
      pointList: [],
      siteStyle: [],
      n: 0,
      map: null,
      draw: null,
      Fill: [0, 180, 255, 0.2],
      Stroke: [0, 180, 255, 0.8],
      SList: [],
      Marker: {},
      is_change_color: false,
      theLayer: null,
      staShow: 0,
      mapPolygon: [],
      mapSet: true,
      mapFull: false,
      p: { count: 0, page: 1, limit: 20, type: "10" },
      loading: false,
      layer_list: []
    };
  },
  created() {
    if (this.$route.query.token !== undefined && this.$route.query.token) {
      this.autoLogin();
    } else {
      // this.init();
      this.http();
      this.getIncident();
      this.getCharge();
      

      // this.source = new ol.source.Vector({ wrapX: true });
    }
  },
  mounted() {
    this.bindmap();
  },
  methods: {
    handleClick(tab, event) {
      // console.log(tab, event);
    },
    autoLogin() {
      if (this.$route.query.token !== undefined && this.$route.query.token) {
        let that = this;
        let data = {
          tk: this.$route.query.token,
          t: this.$route.query.token_time
        };
        service
          .post("/web/login/token", data, {
            timeout: 1000 * 10,
            headers: {
              "Cache-Control": "no-cache"
            }
          })
          .then(res => {
            if (res.code == 200) {
              localStorage.setItem("username", res.data.name);
              localStorage.setItem("userid", res.data.id);
              localStorage.setItem("sid", res.data.session_id);
              localStorage.setItem("usertype", res.data.user_type);
              this.$router.push("/");
              this.$parent.$refs.navbar.username = res.data.name;
              this.init();
              this.http();
              that.loading = false;
            } else {
              that.loading = false;
              that.$message({
                duration: 1500,
                message: res.message,
                type: "error",
                duration: 3000
              });
            }
          })
          .catch(err => {
            that.loading = false;
            that.$message({
              duration: 1500,
              message: err,
              type: "error",
              duration: 3000
            });
          });
      }
    },
    //初始化图层
    bindmap() {
      let that = this;

      that.map = new ol.Map({
        target: "map",
        renderer: "canvas",
        controls: ol.control
          .defaults({
            zoom: 0,
            attribution: false
          })
          .extend([]),
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
          url:
            "http://222.175.243.21:45622/iserver/services/map-HT_Map_SL/rest/maps/HT_Map_White",
          wrapX: false,
          transparent: true
        })
      });
      that.map.addLayer(layers);
      that.getMapDian();
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
        // var vectorSource = new ol.source.Vector({});
        // vectorSource.addFeature(startMarker);
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
            scale: 1
          })
        }),
        visible: true
      });

      this.map.addOverlay(vectorLayer);
      that.layer_list.push({
        // type: option.p1id,
        // type2: option.p2id,
        layer: vectorLayer,
        show: true
      });
      this.map.on("click", function(evt) {
        var feature = that.map.forEachFeatureAtPixel(evt.pixel, function(
          feature,
          layer
        ) {
          return feature;
        });
        var data = feature.N;
        that.info = data.data.info;
        that.dialogVisible = true;
        console.log(data.data.info);
        //data 为 写入的数据
        // that.tu_list = data.data.info;
        // that.showtable = true;
      });
      window.dispatchEvent(new Event('resize'));
    },
    //   //初始化地图
    //  init() {
    //     this.$nextTick(() => {
    //       this.map = new ol.Map({
    //         target: "map",
    //         controls: ol.control.defaults({
    //           attributionOptions: { collapsed: false }
    //         }), //控制按钮

    //         view: new ol.View({
    //           center: [118.024438, 36.963302],
    //           zoom: 11,
    //           minZoom: 11,
    //           projection: "EPSG:4326"
    //         }) //地图初始化设置

    //       });
    //       this.addSourceFromUrls();

    //     });
    //   },
    //   //加载底图
    //   addSourceFromUrls() {

    //     for (let i in this.url) {
    //       var layer = new ol.layer.Tile({
    //         source: new ol.source.TileSuperMapRest({
    //           url: this.url[i]
    //         }),
    //         projection: "EPSG:4326"
    //       });
    //       this.map.addLayer(layer);
    //     }
    //   },

    //   //创建地图点位/
    //   createMark(option) {
    //     let Features = [];
    //     for (let i in option.data) {
    //       var startMarker = new ol.Feature({
    //         name: "p1",
    //         type: "icon",
    //         data:option.data[i],
    //         geometry: new ol.geom.Point(option.data[i].point)
    //       });
    //       Features.push(startMarker);
    //     }

    //     var vectorLayer = new ol.layer.Vector({
    //       source: new ol.source.Vector({
    //         features: Features
    //       }),
    //       style: new ol.style.Style({
    //         image: new ol.style.Icon({
    //           anchor: [0.5, 1],
    //           src: option.src
    //         })
    //       }),
    //       visible: true,
    //     });

    //     this.map.addOverlay(vectorLayer);
    //     // this.map.addEventListener("click", e => {});
    //   },
    //获取点位数据
    getMapDian() {
      let that = this;
      service.post("/web/anjian/dian", {}).then(res => {
        that.createMark({
          data: res.data,
          src: "http://150.242.235.66:3970/icon_GPS.png"
        });
      });
    },
    http() {
      this.$nextTick(() => {
        let myChart1 = echarts.init(document.getElementById("chart1"));
        service.post("/web/Board/getEchars1").then(res => {
          let option1 = {
            legend: {
              right: 0,
              orient: "vertical",
              left: "right",
              data: res.data.label
            },
            //   grid: {
            //   top: "5%",
            //   left: "5%",
            //   right: "5%",
            //   bottom: "5%",
            //   containLabel: true
            // },
            series: [
              {
                type: "pie",
                radius: "60%",
                center: ["40%", "63%"],
                data: res.data.data,
                label: {
                  normal: {
                    formatter: "{b|{b}:}{per|{d}%} ",
                    borderColor: "transparent",
                    borderRadius: 4,
                    rich: {
                      a: {
                        lineHeight: 22,
                        align: "center"
                      },
                      hr: {
                        width: "100%",
                        borderWidth: 1,
                        height: 0
                      },
                      b: {
                        fontSize: 14,
                        lineHeight: 33
                      },
                      c: {
                        fontSize: 14
                      },
                      per: {
                        fontSize: 14,
                        padding: [5, 8],
                        borderRadius: 2
                      }
                    },
                    textStyle: {
                      fontSize: 16
                    }
                  }
                }
              }
            ]
          };
          myChart1.setOption(option1, true);
        });
        let myChart3 = echarts.init(document.getElementById("chart3"));
        let option3 = {
          color: ["#f44"],
          tooltip: {
            trigger: "axis",
            axisPointer: {
              type: "shadow"
            }
          },
          grid: {
            top: "5%",
            left: "5%",
            right: "5%",
            bottom: "5%",
            containLabel: true
          },
          xAxis: [
            {
              type: "category",
              data: [
                "索镇",
                "少海街道",
                "起凤镇",
                "田庄镇",
                "荆家镇",
                "马桥镇",
                "新城镇",
                "唐山镇",
                "果里镇"
              ],
              axisTick: {
                alignWithLabel: true
              }
            }
          ],
          yAxis: [
            {
              type: "value"
            }
          ],
          series: [
            {
              name: "上报数",
              type: "bar",
              barWidth: "35%",
              data: [995, 666, 444, 858, 654, 236, 645, 546, 846]
            }
          ]
        };
        myChart3.setOption(option3);
        window.addEventListener("resize", function() {
          myChart1.resize();
          myChart3.resize();
        });
      });
    },
    drawChart(option) {
      if (!document.getElementById("chart")) return;
      let chart = echarts.init(document.getElementById("chart"));
      chart.clear();
      chart.setOption(option);
      chart.resize();
    },
    resizeChart() {
      if (!document.getElementById("chart")) return;
      let chart = echarts.init(document.getElementById("chart"));
      chart.resize();
    },
    changePoly(el, id) {
      let _val = el.target.checked;
      if (_val) {
        for (let i = 0; i < this.polygon.length; i++) {
          let item = this.polygon[i];
          if (item.id === id) {
            this.mapPolygon.push(item);
            return;
          }
        }
      } else {
        for (let i = 0; i < this.mapPolygon.length; i++) {
          let item = this.mapPolygon[i];
          if (item.id === id) {
            this.mapPolygon.splice(i, 1);
            return;
          }
        }
      }
    },
    getIncident() {
      let that = this;
      service.post("/web/anjian/getIncident", {}).then(res => {
        that.tableData = res.data.data;
        console.log(res);
      });
    },
    getCharge() {
      let that = this;
      service.post("/web/anjian/getCharge", {}).then(res => {
        that.groups = res.data;
        for (let $i = 0; $i < that.list.length; $i++) {
          that.list[$i].number = that.groups[$i];
        }
      });
    },
    goEvent(id) {
      // cq;//超期查询
      // yj;//预警查询
      let listId = {
        "1": () => {
          this.$router.push("/case/eventlist/list");
        },
        "2": () => {
          this.$router.push("/case/eventlist/list");
          sessionStorage.setItem("table_type", "11");
        },
        "3": () => {
          this.$router.push("/case/eventlist/list");
          sessionStorage.setItem("cq", "5");
        },
        "4": () => {
          this.$router.push("/case/eventlist/list");
          sessionStorage.setItem("yj", "6");
        }
      };

      if (listId[id]) {
        listId[id]();
      }
    },
    goDetails(objy) {
      this.$router.push(
        "/case/general/detail?sn=" +
          objy.sn +
          "&id=" +
          objy.id +
          "&lng=" +
          objy.lng +
          "&lat=" +
          objy.lat
      );
    },
    goDetail(objy) {
      switch (objy.stage) {
        case "010":
          // sessionStorage.setItem("ajdetail_id", objy.id);
          // sessionStorage.setItem("ajdetail_stage", objy.stage);
          // this.$router.push("/case/general/detail");
          this.$router.push(
            "/case/general/detail?id=" +
              objy.id +
              "&stage=" +
              objy.stage +
              "&name=" +
              "eventlist"
          );
          break;
        case "020":
          // sessionStorage.setItem("ajdetail_id", objy.id);
          // sessionStorage.setItem("ajdetail_stage", objy.stage);
          // this.$router.push("/case/eventlist/ajdetail");
          this.$router.push(
            "/case/eventlist/list?id=" +
              objy.id +
              "&stage=" +
              objy.stage +
              "&lian=" +
              "020"
          );
          break;
        default:
          // sessionStorage.setItem("ajdetail_id", objy.id);
          // sessionStorage.setItem("ajdetail_stage", objy.stage);
          // this.$router.push("/case/general/detail");
          this.$router.push(
            "/case/general/detail?id=" +
              objy.id +
              "&stage=" +
              objy.stage +
              "&name=" +
              "eventlist"
          );
          break;
      }
    }
  },
  destroyed() {
    window.removeEventListener("resize", this.resizeChart, false);
  }
};
</script>
