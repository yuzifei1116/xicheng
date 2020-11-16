<template>
	<view class="contents">
		<view class="tit">{{info.title}}</view>
		<view class="det">
			<rich-text class="richimg" :nodes="info.content"></rich-text>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				id:0,
				info:[],
			}
		},
		onLoad(opt) {
			this.id = opt.id
			getApp().user_api(true);
			this.noticeIist();
			
		},
		methods: {
			noticeIist(){
				let self = this;
				let path = '/gzh/article_api/visit'
				let data = {id:self.id};
				getApp().http(path, data, function(res) {
					self.info = res;
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

<style lang='scss'>
	page {
		/* background-color: #f7f7f7; */
		padding-bottom: 30upx;
	}
	.tit{
		text-align: center;
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
