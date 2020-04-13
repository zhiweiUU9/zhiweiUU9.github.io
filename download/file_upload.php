<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
	<title>地图</title>
	<link rel="stylesheet" type="text/css" href="../../../static/govern/css/society.css" />
	<link rel="stylesheet" type="text/css" href="../../../static/element/elenent-ui.css" />
	<script src="../../../static/js/echart.js" type="text/javascript" charset="utf-8"></script>
	<script src="../../../static/js/jquery-3.1.1.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="../../../static/js/vue.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="../../../static/js/component/menu/menu1.js"></script>
	<script src="../../../static/js/axios.min.js"></script>
	<script src="../../../static/js/common.js"></script>
	<script src="../../../static/js/request.js"></script>
	<script src="../../../static/element/element-ui.js"></script>
	<style>
		.uni-uploader__file,
		.uploader_video {
			position: relative;
			z-index: 1;
			width: 113.5px;
			height: 113.5px;
			display: inline;
			margin-right: 4px;
		}

		.uni-uploader__img {
			width: 31.3%;
			height: 113.5px;
		}

		.icon-cuo {
			position: absolute;
			right: 0;
			top: 2.5px;
			background: linear-gradient(90deg, rgba(251, 91, 80, 1) 0%, rgba(240, 45, 51, 1) 100%);
			color: #ffffff;
			z-index: 999;
			border-top-right-radius: 10px;
			border-bottom-left-radius: 10px;
		}
		
		
		.app-bg >>>img{
			width: 100%;
			height: 100%;
			-webkit-transform: scale(1.03);
			-moz-transform: scale(1.03);
			-ms-transform: scale(1.03);
			-o-transform: scale(1.03);
			transform: scale(1.03);
		}
		textarea {
			padding: 10px;
		}
		.text-length {
			font-size: 14px;
			z-index: 999;
			width: 100%;
			text-align: right;
			margin-bottom: 10px;
			color: #e4e4e4;
		}
		.warning {
			color: #fe9900;
		}
		.add-img {
			width: 100%;
			padding: 10px;
		}
		.add-img p {
			color: #999;
		}
		.mui-content {
		padding-bottom: 60px;
		}
		.mui-content .idea {
			margin-top: 8px;
			background-color: #FFFFFF;
		}
		.good-item {
			text-align: center;
			width: 33%;
			max-width: 100%;
			overflow: hidden;
			padding-right: 10px;
			padding-bottom: 10px;
			float: left;
		}
		.good-item span {
			font-size: 15px;
			height: 30px;
			line-height: 30px;
			border-radius: 50px;
			display: block;
			width: 100%;
			color: #333;
			overflow: hidden;
			white-space: nowrap;
			text-overflow: ellipsis;
			border: 1px solid #CCCCCC;
		}
		.mui-table {
			padding-top: 10px;
			color: #333;
			padding-left: calc(0.5% + 10px);
		}
		.h-line-behind {
			line-height: 40px;
			padding-left: 10px;
		}
		.question {
			border: 0;
			margin-bottom: 10px;
		}
		.add {
			display: inline-block;
			margin-bottom: 20px;
		}
		.add-image {
			padding-top: 15px;
			margin-left: 10px;
			width: 80px;
			top: 20px;
			height: 80px;
			border: 1px dashed rgba(0, 0, 0, .2);
		}
		.add-image .icon-camera {
			font-size: 24px;
		}
		.good-item .choose {
			color: #fff;
			background-color: #87582E;
			color: #FFF;
			border: 0;
		}
		.mui-btn-block {
			border: 0;
			border-radius: 0;
			background-color: #87582E;
			color: #fff;
			margin-bottom: 0;
			bottom: 0;
		}
		.mui-buttom {
			position: fixed;
			width: 100%;
			bottom: 0;
			z-index: 999;
		}
		/*九宫格*/
		.img-list {
			overflow: hidden;
		}
		.img-list > li {
			float: left;
			width: 32%;
			text-align: center;
			margin-left: 1%;
			margin-top: 1%;
			position: relative;
			list-style: none;
		}
		.img-list > li > div {
			position: absolute;
			left: 0;
			top: 0;
			width: 100%;
			height: 100%;
			overflow: hidden;
		}
		.img-list > li > div .app-bg {
			width: 100%;
			height: 100%;
		}
		.mui-fullscreen {
			z-index: 9999;
		}
		.del {
			position: absolute;
			width: 18px;
			top: 4px;
			right: 17px;
			z-index: 999
		}
	</style>

</head>

<body>
	<div id="app">
		<div class="container" style="height: calc(115vh - 101px);">
			<div class="tit">
				<div class="tit-line"></div>
				<div class="tit-word" :style="{fontSize:fontSize == '' ? 'normal' : fontSize}">窑湾数据填写</div>
			</div>
			<div class="add-content">
				<div class="add-content-two">
					<div class="add-content-one xwidth" :style="{fontSize:fontSize == '' ? 'normal' : fontSize}">
						<div>所属镇：</div>
						<input class="uni-input" v-model="user.zhen_name" readonly placeholder=""
							:style="{fontSize:fontSize == '' ? 'normal' : fontSize}" />
					</div>
					<div class="add-content-one xwidth" :style="{fontSize:fontSize == '' ? 'normal' : fontSize}">
						<div>所属村：</div>
						<input class="uni-input" v-model="user.village_name" readonly placeholder=""
							:style="{fontSize:fontSize == '' ? 'normal' : fontSize}" />
					</div>
				</div>
				<div class="add-content-two">
					<div class="add-content-one xwidth" :style="{fontSize:fontSize == '' ? 'normal' : fontSize}">
						<div>名称：</div>
						<input class="uni-input" v-model="form.name" :readonly="readonly" placeholder=""
							:style="{fontSize:fontSize == '' ? 'normal' : fontSize}" />
					</div>
					<div class="add-content-one xwidth" :style="{fontSize:fontSize == '' ? 'normal' : fontSize}">
						<div>现状：</div>
						<select v-model="form.status" :disabled="readonly"
							:style="{fontSize:fontSize == '' ? 'normal' : fontSize}" style="color: #60656b;">
							<option :value="item.label" v-for="(item,index) in dict.zrzy_status" v-text="item.value">
							</option>
						</select>
					</div>
				</div>
				<div class="add-content-one1" :style="{fontSize:fontSize == '' ? 'normal' : fontSize}">
					<div>地图定位：</div>
					<input class="uni-input" v-model="coords" :readonly="'ture'" placeholder=""
						:style="{fontSize:fontSize == '' ? 'normal' : fontSize}" />
					<img src="../../../static/images/d_location.png" alt="" class="location_icon" @click="coordsClick">
				</div>
				<div class="add-content-one1" :style="{fontSize:fontSize == '' ? 'normal' : fontSize}">
					<div>位置描述：</div>
					<input class="uni-input" v-model="form.address" :readonly="readonly" placeholder=""
						:style="{fontSize:fontSize == '' ? 'normal' : fontSize}" />
				</div>
				<!-- <div class="add-content-one1" :style="{fontSize:fontSize == '' ? 'normal' : fontSize}"
					style="height: auto;">
					<input class="upload" @change='add_img' multiple type="file" accept="image/*" capture="camera">
				</div> -->
				
				<div class="tit">
					<div class="tit-line"></div>
					<div class="tit-word" :style="{fontSize:fontSize == '' ? 'normal' : fontSize}">图片</div>
				</div>
				<input @change="fileChange($event)" type="file" id="upload_file" multiple style="display: none"/>
				<div class="add" @click="chooseType">
					<div class="add-image" align="center">
						<i class="icon-camera"></i> 
						<p class="font13">添加图片</p>
					</div>
				</div>
				<div class="add-img" v-show="imgList.length">
					<p class="font14">图片(最多6张，还可上传<span v-text="6-imgList.length"></span>张)</p>
					<ul class="img-list">
						<li v-for="(url,index) in imgList">
							<img class="del" src="../../../static/images/img_delete.png" @click.stop="delImg(index)"/>
							<img :src="url.file.src" style="width: 80px;">
						</li>
					</ul>
				</div>

				<div style="display: flex;justify-content: space-between;">
					<button @click="check" v-if="type=='check'" class="addsb-content-btn primary">通过</button>
					<button @click="check" v-if="type=='check'" class="addsb-content-btn default">拒绝</button>
				</div>
				<button @click="goSubmit()" v-if="type!='check'" class="addsb-content-btn primary"
					style="width: 100%;bottom: 0px;">{{type=='edit'?'修改':'确认提交'}}</button>
			</div>
		</div>
	</div>
</body>

</html>

<script>
	$(function () {
		new Vue({
			el: "#app",
			name: "Feedback",
			data() {
				return {
					readonly: false,
					type: '',
					form: {
						name: '',
						status: '',
						XPos: '',
						YPos: '',
						comment: '',
						address: '',
						villageId: '',
						townId: '',
						userId: '',
						fileList: '',
					},
					dict: {},
					user: {},
					coords: '',
					fontSize: '',
					
					showFace: false,
					imgList: [],
					size: 0,
					limit:6, //限制图片上传的数量
					tempImgs:[]

				}
			},
			created() {
				let that = this;
				this.type = getUrlParam('type') ? getUrlParam('type') : 'add';
				this.menu = getUrlParam('menu');
				this.http();
				if (this.type == 'check') {
					this.readonly = true;
				}
				if (navigator.geolocation) {
					navigator.geolocation.getCurrentPosition((position) => {
						that.form.XPos = position.coords.longitude;
						that.form.YPos = position.coords.latitude;
						that.coords = position.coords.longitude + ',' + position.coords.latitude;
					});
				}
				this.fontSize = getUrlParam('fontSize') + 'px';
				// this.fontSize = 25+'px';
			},
			methods: {
				add_img(event) {
					let that = this;

					let reader = new FileReader();
					let img1 = event.target.files;
					
					let form = new FormData();
					for (var i = 0; i < img1.length; i++) {
						form.append('file[]', img1[i]);
					}

					form.append('token', getUrlParam("sid"));

					uploadRequest('/Yaowan/filesave', form, function (res) {
						that.form.fileList = JSON.stringify(res.data);
					}, function (errcode, errmsg) {
						alert('code ' + errcode + ' ,meaasge ' + errmsg)
					})

				},
				http() {
					let that = this;
					postRequest('/base/getDict', {
						type: 'zrzy_status'
					}, function (res) {
						that.dict = res.data;
						that.user = res.user;
						that.form.villageId = res.user.village_id
						that.form.townId = res.user.zhen_id
						that.form.userId = res.user.id
					}, function (errcode, errmsg) {
						alert('code ' + errcode + ' ,meaasge ' + errmsg)
					})
					if (that.type != 'add') {
						getRequest('/Yaowan/saveData', {
							id: getUrlParam('id')
						}, function (res) {
							console.log('2222222222');
							console.log(res)
							setTimeout(() => {
								that.form = res.data;
								if(res.data.fileList!='' && res.data.fileList!=null){
									that.imgList=JSON.parse(res.data.fileList)
								}
							}, 500);
						}, function (errcode, errmsg) {
							alert('code ' + errcode + ' ,meaasge ' + errmsg)
						})
					}
				},
				goSubmit() {
					this.form.fileList = JSON.stringify(this.imgList);
					let that = this;
					postRequest('/Yaowan/saveData', this.form, function (res) {
						console.log(res);return false;
						window.location.href = "../list/submitted.html?sid=" + getUrlParam('sid') + '&menu=' + that.menu;
					}, function (errcode, errmsg) {
						alert('code ' + errcode + ' ,meaasge ' + errmsg)
					})
				},
				check(e) {
					let that = this;
					let state = '';
					if (e.target.innerText == '通过') state = 1;
					else state = 2;
					postRequest('/Yaowan/check', {
						state: state,
						id: getUrlParam('id')
					}, function (res) {
						window.location.href = "../list/submitted.html?sid=" + getUrlParam('sid') + '&menu=' + that.menu;
					}, function (errcode, errmsg) {
						alert('code ' + errcode + ' ,meaasge ' + errmsg)
					})
				},

				//地图定位点击事件
				coordsClick() {
					location.href = '/page/govern/info/ywadd.html?longitude=' + this.form.XPos + '&latitude=' + this.form.YPos;
				},
				
				
				chooseType() {
					document.getElementById('upload_file').click();
				},
				fileChange(el) {
					if (!el.target.files[0].size) return;
					this.fileList(el.target);
					el.target.value = ''
				},
				fileList(fileList) {
					let files = fileList.files;
					for (let i = 0; i < files.length; i++) {
						//判断是否为文件夹
						if (files[i].type != '') {
							this.fileAdd(files[i]);
						} else {
						//文件夹处理
							this.folders(fileList.items[i]);
						}
					}
				},
				//文件夹处理
				folders(files) {
					let _this = this;
					//判断是否为原生file
					if (files.kind) {
						files = files.webkitGetAsEntry();
					}
					files.createReader().readEntries(function (file) {
						for (let i = 0; i < file.length; i++) {
							if (file[i].isFile) {
								_this.foldersAdd(file[i]);
							} else {
								_this.folders(file[i]);
							}
						}
					});
				},
				foldersAdd(entry) {
					let _this = this;
					entry.file(function (file) {
						_this.fileAdd(file)
					})
				},
				fileAdd(file) {
					if (this.limit !== undefined) this.limit--;
					if (this.limit !== undefined && this.limit < 0) return;
					//总大小
					this.size = this.size + file.size;
					//判断是否为图片文件
					if (file.type.indexOf('image') == -1) {
						this.$dialog.toast({mes: '请选择图片文件'});
					} else {
						let reader = new FileReader();
						let image = new Image();
						let _this = this;
						reader.readAsDataURL(file);
						reader.onload = function () {
							file.src = this.result;
							image.onload = function(){
								let width = image.width;
								let height = image.height;
								file.width = width;
								file.height = height;
								_this.imgList.push({file});
								// console.log( _this.imgList);
							};
							image.src= file.src;
						}
					}
				},
				delImg(index) {
					this.size = this.size - this.imgList[index].file.size;//总大小
					this.imgList.splice(index, 1);
					if (this.limit !== undefined) this.limit = 6-this.imgList.length;
				},
				displayImg() {
				}
			}
		})
	})
</script>