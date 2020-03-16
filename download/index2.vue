<template>
  <div class="container-sec">
    <div class="set-content box-shadow">
      <div class="th-main" style="height: 100% !important;">
        <div class="post-tree" v-loading="treeLoad">
          <el-tree
            :data="data"
            node-key="id"
            label="name"
            ref="tree"
            :default-expanded-keys="[1]"
            @node-click="nodeclick"
            highlight-current
            :props="defaultProps"
          ></el-tree>
        </div>
        <div class="blocks">
          <div id="map" style="width: 100%;height:100%;border:1px solid #C5D0DC;"></div>
        </div>
      </div>
    </div>
  </div>
</template>
<style scoped>
.set-content {
  padding-top: 0;
}
.post-tree {
  flex: 0.2;
  border-right: 1px solid #ccc;
  padding-top: 30px;
  height: 100%;
}
.blocks {
  display: flex;
  flex: 1;
}
</style>
<script>
import { service } from "@/utils/request";
import page from "@/components/pagination";
import mixin from "@/mixins/case";
import cdmixin from "@/mixins/listCountDown";
import detailsSingle from "@/components/m_evidence/detailsSingle";
import detailsDouble from "@/components/m_evidence/detailsDouble";
import alreadySingle from "@/components/m_evidence/alreadySingle";
import alreadyDouble from "@/components/m_evidence/alreadyDouble";

export default {
  components: {
    page,
    detailsSingle,
    detailsDouble,
    alreadySingle,
    alreadyDouble
  },
  mixins: [mixin, cdmixin],
  data() {
    return {
      treeLoad: false,
      data: [],
      defaultProps: {
        label: "name"
      },
      points: [],
      startD: false,
      form: {
        id: "",
        parent: ""
      },
      //
      source: null,
      url: [
        "http://222.175.243.21:45622/iserver/services/map-HT_Map_SL/rest/maps/HT_Map_White"
      ],
      pointList: [],
      siteStyle: [],
      n: 0,
      map: null,
      draw: null,
      Fill: [0, 180, 255, 0.2],
      Stroke: [0, 180, 255, 0.8],
      SList: [],
      mapLayer: [],
      theLayer: null,
      
    };
  },
  created() {
    this.source = new ol.source.Vector({ wrapX: true });

    this.http();

    this.init();

    this.getMapInfo();
  },
  methods: {
    http() {
      let that = this;
      service.post("/web/Village/tree").then(res => {
        this.data = res.data;
      });
      service.post("/web/User/getUserList").then(res => {
        if (res.code == 200) {
          this.userList = res.data;
        }
      });
    },
    nodeclick(a, b) {
      let node = b.parent.data;
      console.log(a, b);
      
      if (a.lng) {
        this.parentName = a.name;
        this.form.parent = a.id;
        if (a.pid == 0) {
          var level = 1;
        } else if (a.pid == 1) {
          var level = 2;
        } else {
          var level = 3;
        }
        // this.getMapInfo(a.id, true);
        this.setZoomCenter(a);
      } else {
        var level = 4;
        if (node) {
          this.parentName = node.name;
          this.form.parent = node.id;
          this.setZoomCenter(node);
        }
      }
      this.getMapInfo(a.id, level);
      // if (node) {
        // this.parentName = node.name;
        // this.form.parent = node.id;

        
      // }
      // }
    },
    setZoomCenter(c) {
      this.map.getView().setCenter([parseFloat(c.lng), parseFloat(c.lat)]);
      if (c.pid == 0 || c.pid == null) {
        this.form.level = 1;
        this.map.getView().setZoom(11);
      } else if (c.pid == 1) {
        this.form.level = 2;
        this.map.getView().setZoom(13);
      } else {
        this.form.level = 3;
        this.map.getView().setZoom(15);
      }
    },
    getMapInfo(parent, end) {
     
      // var search = { parent: parent };
      // if (!end) {
      var search = { id: parent, level: end };
      // }
      service.post("/web/Map/getMapPoint", search).then(res => {
        if (res.code == 200) {
          this.n = 0;
          this.pointList = [];
          this.siteStyle = [];
          this.clearAllLayer();
          for (let i in res.data) {
            this.pointList.push(JSON.parse(res.data[i].lnglat));
            let color = res.data[i].color;
            color = color.replace("rgb(", "");
            color = color.replace(")", "");
            color = color.replace(/ /g, "");
            color = color.split(",");
            this.siteStyle.push(
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
                  color:
                    "rgba(" +
                    color[0] +
                    "," +
                    color[1] +
                    "," +
                    color[2] +
                    ",0.8)",
                  width: 1
                }),
                text: new ol.style.Text({
                     text: this.parentName,
                     scale: 1.3,
                     fill: new ol.style.Fill({
                       color: '#000000'
                     }),
                     stroke: new ol.style.Stroke({
                       color: '#FFFF99',
                       width: 3.5
                     })
                 })
              })
            );
          }
          this.createPolygon();
        }
      });
    },
    clearAllLayer() {
      //清空查询出的网格图层
      for (let i in this.mapLayer) {
        this.map.removeLayer(this.mapLayer[i]);
      }
    },
    init() {
      this.$nextTick(() => {
        this.map = new ol.Map({
          target: "map",
          controls: ol.control.defaults({
            attributionOptions: { collapsed: false }
          }), //控制按钮

          view: new ol.View({
            center: [118.024438, 36.963302],
            zoom: 12,
            minZoom: 12,
            projection: "EPSG:4326"
          }) //地图初始化设置
          // overlays: [overlay]
        });
        this.addSourceFromUrls();
        // this.layerService(overlay);
      });
    },
    addSourceFromUrls() {
      //加载底图
      for (let i in this.url) {
        var layer = new ol.layer.Tile({
          source: new ol.source.TileSuperMapRest({
            url: this.url[i]
          }),
          projection: "EPSG:4326"
        });
        this.map.addLayer(layer);
      }
    },
    createPolygon() {
      //根据点位生成多边形
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
          var feature = new ol.Feature({
            geometry: geometry
          }); //点位转换为图形对象
          this.SList[c].addFeatures([feature]); //将图形添加至目标图层
          c++;
        }
      }
    },
    setPolygonEndStyle() {
      //设置多边形绘制结束时的样式
      let color = this.form.color;
      color = color.replace("rgb(", "");
      color = color.replace(")", "");
      color = color.replace(/ /g, "");
      color = color.split(",");
      let polygonLayer = new ol.layer.Vector({
        source: this.source,
        /*图形绘制好时最终呈现的样式,显示在地图上的最终图形*/
        style: new ol.style.Style({
          fill: new ol.style.Fill({
            color:
              "rgba(" + color[0] + "," + color[1] + "," + color[2] + ",0.1)"
          }),
          stroke: new ol.style.Stroke({
            color:
              "rgba(" + color[0] + "," + color[1] + "," + color[2] + ",0.8)",
            width: 1
          }) //边框线
          // image: new ol.style.Circle({
          //   radius: 5,//半径 px
          //   fill: new ol.style.Fill({
          //     color: "red"//填充色
          //   })
          // })//指针样式
        })
      });
      this.map.addLayer(polygonLayer);
    },

    // layerService(overlay) {
    //   //获取底图信息
    //   new ol.supermap.LayerInfoService(this.url).getLayersInfo(function(
    //     serviceResult
    //   ) {
    //     overlay.setPosition([0, 0]);
    //   });
    // },
    startDraw() {
      //开始绘制
      if (this.theLayer == null) {
        var vector = new ol.layer.Vector({
          source: this.source
        });
        this.theLayer = vector;
        this.map.addLayer(vector);
      }

      this.setPolygonEndStyle();
      this.addInteraction();
      this.startD = true;
      this.is_change_color = true;
    },
    endDraw() {
      //结束绘制
      this.map.removeInteraction(this.draw);
      this.startD = false;
    },
    clearLabel() {
      //清空内容
      this.source.clear();
      this.map.removeInteraction(this.draw);
      this.endDraw();
      this.form.lnglat = [];
      this.is_change_color = false;
    },
    addInteraction() {
      //绘制多边形
      let color = this.form.color;
      color = color.replace("rgb(", "");
      color = color.replace(")", "");
      color = color.replace(/ /g, "");
      color = color.split(",");
      this.draw = new ol.interaction.Draw({
        source: this.source,
        type: "Polygon",
        style: new ol.style.Style({
          fill: new ol.style.Fill({
            color:
              "rgba(" + color[0] + "," + color[1] + "," + color[2] + ",0.1)"
          }),
          stroke: new ol.style.Stroke({
            color:
              "rgba(" + color[0] + "," + color[1] + "," + color[2] + ",0.8)",
            width: 1
          })
        })
      });
      let that = this;
      this.draw.on("drawend", function(e) {
        var feature = e.feature;
        var geometry = feature.getGeometry();
        var coordinate = geometry.getCoordinates();
        that.form.lnglat.push(JSON.stringify(coordinate));
      });
      this.map.addInteraction(this.draw);
    }
  }
};
</script>
<style  scoped>
.set-content {
  padding-left: 10px;
}
.th-main {
  height: 92% !important;
}
</style>
