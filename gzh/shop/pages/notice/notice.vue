<template>
	<view class="con">
		<view v-for="(item,index) in noticeInfo">
			<!-- {{item.status}} -->
			<view class="con1">
				<uni-collapse @change="change(item)">
				    <uni-collapse-item :title="item.title" :status="item.status">
				        <uni-list>
				            <uni-list-item  :note="item.content"  show-extra-icon="true" :extra-icon="{color: '#4cd964',size: '22',type: 'spinner'}"></uni-list-item>
				        </uni-list>
				    </uni-collapse-item>
				</uni-collapse>
			</view>
		</view>
		
	</view>
</template>

<script>
	import uniCollapse from '@/components/uni-collapse/uni-collapse.vue'
	import uniCollapseItem from '@/components/uni-collapse-item/uni-collapse-item.vue'
	export default {
		components: {
			uniCollapse,
			uniCollapseItem
		},
		data() {
			return {
				noticeInfo:[],
			}
		},
		onLoad() {
			getApp().user_api(true);
			this.noticeIist();
		},
		methods: {
			noticeIist(){
				let self = this;
				let path = '/gzh/user_api/notice_list'
				getApp().http(path, {}, function(res) {
					self.noticeInfo = res;
				})
			},
			change(item){
				let self = this;
				console.log('现实',item);
				if(item.status <= 0){
					let path = '/gzh/user_api/read_notice'
					let data = {nid:item.id};
					getApp().http(path, data, function(res) {
						self.noticeIist();
					})
				}
			},

		}
	}
</script>

<style lang='scss'>
	page {
		background-color: #f7f7f7;
		padding-bottom: 30upx;
	}

	@import url("notice.css");
</style>
