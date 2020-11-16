<template>
    <view class="content">
        <swiper class="swiper" :indicator-dots="true"  previous-margin="60upx" next-margin="60upx">
            <swiper-item v-for="(item,index) in imgsrc" :key="index">
                <view class="post">
                    <view class="content" style="text-align: center;width: 80%;margin: 0 auto;">
                        <p style="font-size: 30upx;margin-top: 70upx;margin-bottom: 30upx;">长按保存图片至相册</p>
                        <view style="background: #f5f5f5;text-align: center;">
                                <img :src="item" style="width: 100%;height: 100%;" />
                        </view>
                    </view>
                </view>
            </swiper-item>
        </swiper>
    </view>
</template>

<script>
    import canvas_x from '../../common/canvas_x.js';
    export default {
        data() {
            return {
                imgsrc:[],
                beijing:[],
                val: "",
                userid: 0,
                qrShow: false,
                uid:'',
				userInfo:[],
            };
        },
        onLoad(option) {
			
            uni.showLoading({
                // title: "财源滚滚来...",
                mask:true
            })
			
			this.my();
            
        },
        methods: {
			
			my(){
				let self = this;
				let path = '/gzh/user_api/my'
				getApp().http(path, {}, function(res) {
					console.log('个人信息',res)
					self.userInfo = res;
					self.uid = res.uid
					self.haiB();
				})
			},
			
			haiB(){
				console.log('沙发上对方',this.userInfo,this.webUrl)
				this.val = this.webUrl+'/gzh/?spuid='+this.userInfo.uid,
				uni.request({
				    url: this.webUrl+'/gzh/auth_api/get_spread_poster',
				    data: {},
				    header: {
				        'Content-Type': 'application/x-www-form-urlencoded'
				    },
				    success: (res) => {
				        console.log("huoqu ",res.data);
						
				        this.beijing = res.data;
				    }
				});
			},
			
			
            createposter: function(img) {
                uni.showLoading({
                    title: "加载中",
                    mask:true
                })
                canvas_x.makeImage({
                    type: 'url',
                    parts: [{
                            type: 'image',
                            // url: '../../static/image/beijing.jpg',
                            url: img,
                            width: 700,
                            height: 1200,
                            // backgroundSize:680,
                        },
                        {
                            type: 'qrcode',
                            text: this.val,
                            x: 45,
                            y: -58,
                            width: 180,
                            height: 180,
                            padding: 0,
                            background: '#fff',
                            level: 3
                        },
                        {
                            type: 'text',
                            text: this.uid,
                            textAlign: 'left',
                            lineAlign: 'center',
                            x: 430,
                            y: -68,
                            color: 'red',
                            size: '50px',
                            // bold: true
                        }
                        
                    ],
                    width: 700,
                    height: 1200
                }, (err, data) => {
					
					console.log('图图图图图图',data)
					
                    this.imgsrc.push(data);
                    uni.hideLoading();
                })
            }
        },
        watch:{
            dataRange:function(){
				console.log('水电费水电费水电费',this.beijing)
                for(let i=0,bj=this.beijing.data;i<bj.length;i++){
					console.log('循环',bj[i]['pic'])
                    this.createposter(bj[i]['pic']);
                }
            }
        },
        computed: {
            dataRange () {
              const { beijing, uid} = this
			  console.log('测试',beijing,uid)
              return {
                beijing,
                uid
              }
            }
        }
    };
</script>

<style lang="scss">
    .swiper{
        height: 1200upx;
    }
    .tupian {
        // width: 100%;
        // height: 1360upx;
        // background-image: url('../../static/mg-h5hb/tutu3.png');
        // background-size: 750upx 1360upx;
    }
	.post{
		width: 100%;
	}

</style>
