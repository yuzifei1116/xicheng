<template>
    <view class="conbox">
        <view class="container">
            <!-- 背景 -->
			<!-- <image src="../../static/img/bg.png"></image> -->
            <image src="../../static/img/bg.png" class="cont" mode=""></image>
            <image src="../../static/img/caidai.png" class="caidai" mode=""></image>
			
            <view class="main" style="padding-top: 50upx;">
				
				<view class="cl-num aces-space-between">
					<view class="cli-num">次数：<text class="red">{{noticeInfo.prize_num}}</text></view>
				</view>
				
                <view class="canvas-container">

                    <view :animation="animationData" class="canvas-content" id="zhuanpano" style="">
                        <view class="canvas-line">
                            <view class="canvas-litem" v-for="(item,index) in list" :key="index"
                                  :style="{transform:'rotate('+(index * width + width / 2)+'deg)'}"></view>
                        </view>

                        <view class="canvas-list">
                            <view class="canvas-item" :style="{transform: 'rotate('+(index * width)+'deg)', zIndex:index, }" v-for="(iteml,index) in list" :key="index">
                                <view class="canvas-item-text" :style="'transform:rotate('+(index )+')'">
                                    <text class="b">{{iteml.prize_name}}</text>
                                    <!-- <text class="icon-awrad iconfont " :class="iteml.icon"></text> -->
									<image class="icon-img" :src="indexUrl(iteml.image)"></image>
                                </view>
                            </view>
                        </view>
                    </view>

                    <view @tap="playReward" class="canvas-btn" v-bind:class="btnDisabled">开始 </view>
					 <!-- <view @tap="playReward" class="canvas-btn" >开始 </view> -->
                </view>
            </view>
            <!-- 规则 -->
            <view class="guize" style="margin-top: 100upx;">
                <view class="title">
                    {{noticeInfo.title}}
                </view>
                <view class="g_item">
                    活动开始时间：{{noticeInfo.start_time}}
                </view>
                <view class="g_item">
                    活动结束时间：{{noticeInfo.end_time}}
                </view>
				<view class="g_item" v-if="noticeInfo.use_score > 0">
					每次抽奖消耗{{noticeInfo.use_score}}积分
				</view>
                <view class="g_item">
                    {{noticeInfo.description}}
                </view>
            </view>
        </view>

		<view class="modal" v-if="allhidden">
			<view class="modal-box"  @click="hideAllClass" ></view>
			<view class="modal-body " >
				<view class="mod-tit">恭喜您抽中了{{draw.prize_name}}</view>
				<view class="modi">
					<image class="mod-img" :src="indexUrl(draw.image)"></image>
				</view>
				<view class="modde">*请到个人信息页面补全信息</view>
				<view class="mod-btn"  @click="hideAllClass">确定</view>
			</view>
		</view>

    </view>
</template>

<script>
	export default {
		data() {
			return {
				list: [],
				width: 0,
				animationData: {},
				btnDisabled: '',
				act_id:0,
				noticeInfo:[],
				checkClassNum:1,
				allhidden:false,
				draw:[],
				time:3000,
			}
		},
		onLoad(option) {
			console.log('说得对覆盖',option)
			getApp().user_api(true);
			this.act_id = option.id
			this.activityDetail();
		},
		methods: {
			
			activityDetail(){
				let self = this;
				let path = '/gzh/activity_api/activity_detail'
				let data = {act_id:self.act_id}
				getApp().http(path, data, function(res) {
					console.log('个人信息',res)
					self.noticeInfo = res;
					self.list = res.prize;
					
					
					// 获取奖品列表
					self.width = 360 / self.list.length
				})
			},
			
			animation(index, duration){
				//中奖index
				var list = this.list;
				// var awardIndex = 1 || Math.round(Math.random() * (awardsNum.length - 1)); //随机数
				var runNum = 4; //旋转8周

				// 旋转角度
				this.runDeg = this.runDeg || 0;
				this.runDeg = this.runDeg + (360 - this.runDeg % 360) + (360 * runNum - index * (360 / list.length)) +1
				//创建动画
				var animationRun = uni.createAnimation({
				  duration: duration,
				  timingFunction: 'ease'
				})
				animationRun.rotate(this.runDeg).step();
				this.animationData = animationRun.export();
				// this.btnDisabled = 'disabled';
		
			},
			//发起抽奖
			playReward(){
				
				let self = this;
				
				// 开始
				let str = self.noticeInfo.start_time;
				let date = new Date(str); 
				let time = Date.parse(date);
				let start_time = time / 1000;
				
				// 结束
				let end_str = self.noticeInfo.end_time;
				let end_date = new Date(end_str); 
				let end_times = Date.parse(end_date);
				let end_time = end_times / 1000;
				// 当前
				let not_time = new Date()
				let n_time =  Date.parse(not_time);
				let not_times = n_time / 1000;
				
				if(not_times < start_time){
					getApp().showTip('活动未开始','none')
					return;
				}
				if(not_times > end_time){
					getApp().showTip('活动已结束','none')
					return;
				}
				
				// console.log(start_time,'开始时间')
				// return ;
				
				if(self.noticeInfo.prize_num <= 0){
					this.btnDisabled = 'disabled';
					getApp().showTip('抽奖次数已用完','none')
					return;
				}
				
				let list = self.list;
				let path = '/gzh/activity_api/prize_draw'
				let data = {act_id:self.act_id}
				getApp().http(path, data, function(res) {
					self.draw = res;
					self.noticeInfo.prize_num = self.noticeInfo.prize_num-1
					let ind = self.inArray(res.prize_id,list)
					let index = ind, duration = self.time
					self.animation(index, duration)
					setTimeout(() => {
						self.allClass();
					}, self.time);
					
				})
				

				// setTimeout(() => {
				//   uni.showModal({content: this.list[index].isNoPrize ? '抱歉，您未中奖' : '恭喜，中奖'})
				//   this.btnDisabled = '';
				// }, duration + 1000)

			},
			inArray(search,array){
				for(var i in array){
					if(array[i].id==search){
						return i;
					}
				}
				return false;
			},
			
			allClass(){
				var that = this;
				that.allhidden = true
			},
			hideAllClass(){
				console.log('隐藏隐藏隐藏隐藏')
				var that = this;
				console.log(this)
				that.allhidden = false
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
<style scoped>
	
	@import url("draw.css");

   

</style>
