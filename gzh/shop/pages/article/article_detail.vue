<template>
	<view class="contents1">
		<view class="tit">{{info.title}}</view>
		<view class="det">
			<!-- <rich-text class="richimg" :nodes="info.content"></rich-text> -->
			<jyf-parser :html="info.content" ref="article"></jyf-parser>
		</view>
	</view>
</template>

<script>
	import jyfParser from '@/components/jyf-parser/jyf-parser';
	export default {
		components: {
			jyfParser
		},
		data() {
			return {
				id:0,
				info:[],
			}
		},
		onLoad(opt) {
			this.id = opt.id
			this.noticeIist();
			
		},
		methods: {
			noticeIist(){
				let self = this;
				let path = '/gzh/article_api/visit'
				let data = {id:self.id};
				getApp().http(path, data, function(res) {
					self.info = res;
					
					let simg = res.image_input;
					let slider_image = simg.replace(/http[s]?:\/\/[^\/]+/,'')
					// getApp().user_api(true);
					getApp().user_api(true,res.title,res.synopsis,slider_image);
					
					wx.setNavigationBarTitle({
					  title: res.title
					})
					
					console.log(res,'详情详情详情')
				})
			},
			indexUrl(icon){
				let http = icon.indexOf('http')
				if(http == -1){
					return this.webUrl +'/'+icon
				}else{
					return icon;
				}
			},
			
			
		}
	}
</script>

<style >
	page {
		/* background-color: #f7f7f7; */
		padding-bottom: 30upx;
	}
	.contents1{
		margin-top:60upx;
	}
	.tit{
		text-align: center;
		/* margin-top: 20upx; */
		padding:0upx 60upx;
	}
	.det{
		/* border:1px solid red; */
		padding:25upx;
		font-size:28upx;
	}
	/deep/ .richimg img{
		width:100%;
	}
</style>
