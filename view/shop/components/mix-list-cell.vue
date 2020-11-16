<template>
	<view class="content">
		
		<view class="mix-list-cell acea-row" :class="border" @click="eventClick(url)" hover-class="cell-hover"  :hover-stay-time="50">
			<!-- <text
				v-if="icon"
				class="cell-icon yticon"
				:style="[{
					color: iconColor,
				}]"
				:class="icon"
			></text> -->
			<view class="icimg">
				<image :src="icon" class="iconimg"></image>
			</view>
			<!-- <view> -->
				<view class="cell-tit clamp">{{title}}</view>
				<view v-if="tips" class="cell-tip">{{tips}}</view>
			<!-- </view> -->
			<text class="cell-more yticon"
				:class="typeList[navigateType]"
			></text>
		</view>

	</view>
</template>
 
<script>
	/**
	 *  简单封装了下， 应用范围比较狭窄，可以在此基础上进行扩展使用
	 *  比如加入image， iconSize可控等
	 */
	export default {
		data() {
			return {
				typeList: {
					left: 'icon-zuo',
					right: 'icon-you',
					up: 'icon-shang',
					down: 'icon-xia'
				},
			}
		},
		props: {
			icon: {
				type: String,
				default: ''
			},
			title: {
				type: String,
				default: '标题'
			},
			url: {
				type: String,
				default: '标题'
			},
			tips: {
				type: String,
				default: ''
			},
			navigateType: {
				type: String,
				default: 'right'
			},
			border: {
				type: String,
				default: 'b-b'
			},
			hoverClass: {
				type: String,
				default: 'cell-hover'
			},
			iconColor: {
				type: String,
				default: '#333'
			}
		},
		methods: {
			eventClick(url){
				// this.$emit('eventClick');
				
				console.log(url)
				uni.navigateTo({
					url: url
				})
				
			}
		},
	}
</script>

<style lang='scss'>
	.icimg{
		padding-top:20upx;
		margin-right:20upx;
		/* border:1px solid red; */
	}
	.iconimg{
		width:35upx;
		height:35upx;
		/* line-height: 50upx; */
	}
	.icon .mix-list-cell.b-b:after{
		left: 90upx;
	}
	.mix-list-cell{
		/* display:flex;
		align-items:baseline; */
		padding: 0upx 30upx 0px 30upx ;
		/* height:90upx; */
		/* line-height:20upx; */
		/* position:relative; */
		border-bottom:1px solid #FAFAFA;
		/* border:1px solid red; */
		
		/* &.cell-hover{
			background:#fafafa;
		}
		&.b-b:after{
			left: 30upx;
		} */

		.cell-icon{
			align-self:center;
			width:56upx;
			max-height:60upx;
			font-size:38upx;
		} 
		.cell-more{
			align-self: center;
			font-size:30upx;
			color:$font-color-base;
			margin-left:$uni-spacing-row-sm;
		}
		.cell-tit{
			flex: 1;
			font-size: $font-base;
			color: $font-color-dark;
			margin-right:10upx;
			/* margin-top:10upx; */
			line-height: 90upx;
		}
		.cell-tip{
			font-size: $font-sm+2upx;
			color: $font-color-light;
			line-height: 90upx;
		}
	}
</style>
