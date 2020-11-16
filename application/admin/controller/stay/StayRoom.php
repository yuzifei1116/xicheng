<?php

namespace app\admin\controller\stay;


use app\admin\controller\AuthController;
use app\core\model\stay\StayRoom as StayRoomModel;
use app\core\model\stay\StayType as StayTypeModel;
use service\FormBuilder;
use service\JsonService;
use service\UtilService;
use think\Request;
use think\Url;

class StayRoom extends AuthController
{
    public function index()
    {
        $type = StayTypeModel::getRoomTypeList(0); //获取房间类型列表
        $this->assign('type',$type);
        return $this->fetch();
    }

    public function room_list()
    {
        $where = UtilService::getMore([
            ['type_id',0],
            ['status',''],
            ['number',''],
            ['page',1],
            ['limit',20]
        ]);
        return JsonService::successlayui(StayRoomModel::getRoomList($where));
    }

    /**
     * 添加
     */
    public function create()
    {
        $field = [
            FormBuilder::select('type_id','房间类型')->setOptions(function(){
                $list = StayTypeModel::getRoomTypeList(0); //获取房间类型列表
                $menus = [];
                foreach ($list as $menu){
                    $menus[] = ['value'=>$menu['id'],'label'=>$menu['title']];
                }
                return $menus;
            })->filterable(1)->required(),
            FormBuilder::input('number','房间号')->col(24)->required(),
            FormBuilder::input('title','房间标题')->col(24)->required(),
            FormBuilder::input('bed','床铺类型')->col(24)->placeholder('例:单人床')->required(),
            FormBuilder::frameImageOne('image','图片',Url::build('admin/widget.images/index',array('fodder'=>'image')))->width('100%')->height('500px')->icon('image')->required(),
            FormBuilder::number('price','价格')->col(8)->required(),
            FormBuilder::number('original_price','原价')->col(8)->required(),
            FormBuilder::number('give_integral','赠送积分',0)->col(8),
            FormBuilder::number('space','面积(㎡)',0)->required()->col(8),
            FormBuilder::number('sort','排序',0)->col(8),
            FormBuilder::radio('breakfast','有无早餐',0)->options([['label'=>'无','value'=>0],['label'=>'有','value'=>1]])->required()->col(8),
            FormBuilder::radio('preferential','超值优惠',0)->options([['label'=>'否','value'=>0],['label'=>'是','value'=>1]])->required()->col(8),
            FormBuilder::radio('status','状态',0)->options([['label'=>'未入住','value'=>0],['label'=>'已入住','value'=>1],['label'=>'禁用','value'=>2]])->required()->col(8)
        ];
        $form = FormBuilder::make_post_form('添加房间',$field,Url::build('save'),2);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $data = UtilService::postMore([
            ['type_id',0],
            ['number',0],
            ['title',''],
            ['bed',''],
            ['image',[]],
            ['price',0],
            ['space',0],
            ['original_price',0],
            ['give_integral',0],
            ['sort',0],
            ['breakfast',0],
            ['preferential',0],
            ['status',0]
        ],$request);
        if(!$data['type_id']) return JsonService::fail('请选择房间类型');
        if($data['number'] == '') return JsonService::fail('请输入房间号');
        if($data['title'] == '') return JsonService::fail('请输入房间标题');
        if($data['bed'] == '') return JsonService::fail('请输入床铺类型');
        if(!$data['space']) return JsonService::fail('请输入房间面积');
        if(!$data['price']) return JsonService::fail('请输入房间价格');
        if($data['image'][0] == '') return JsonService::fail('请上传图片');
        if($data['price'] > $data['original_price']) return JsonService::fail('房间价格不能大于原价');
        if (StayRoomModel::be(['number'=>$data['number']])) return JsonService::fail('房间号重复');

        if ($data['status'] == 0 || $data['status'] == 1){
            $type = StayTypeModel::get($data['type_id']);
            if ($type['status'] == 0) return JsonService::fail('此类型房间已禁用,不能选择此状态');
        }
        if($data['sort'] < 0) $data['sort'] = 0;
        $data['image'] = $data['image'][0];
        $res = StayRoomModel::set($data);
        if ($res){
            return JsonService::successful('添加房间成功!');
        }else{
            return JsonService::fail('添加房间失败!');
        }
    }

    public function edit($id)
    {
        if (!$id) return JsonService::fail('数据错误');
        $room = StayRoomModel::get($id);
        if (empty($room)) return JsonService::fail('数据错误');
        $field = [
            FormBuilder::select('type_id','房间类型',(string)$room->getData('type_id'))->setOptions(function() use($id){
                $list = StayTypeModel::getRoomTypeList(0); //获取房间类型列表
                $menus = [];
                foreach ($list as $menu){
                    $menus[] = ['value'=>$menu['id'],'label'=>$menu['title']];
                }
                return $menus;
            })->filterable(1)->required(),
            FormBuilder::input('number','房间号',$room->getData('number'))->col(24)->required(),
            FormBuilder::input('title','房间标题',$room->getData('title'))->col(24)->required(),
            FormBuilder::input('bed','床铺类型',$room->getData('bed'))->col(24)->placeholder('例:单人床')->required(),
            FormBuilder::frameImageOne('image','图片',Url::build('admin/widget.images/index',array('fodder'=>'image')),$room->getData('image'))->width('100%')->height('500px')->icon('image')->required(),
            FormBuilder::number('price','价格',$room->getData('price'))->col(8)->required(),
            FormBuilder::number('original_price','原价',$room->getData('original_price'))->col(8)->required(),
            FormBuilder::number('give_integral','赠送积分',$room->getData('give_integral'))->col(8),
            FormBuilder::number('space','面积(㎡)',$room->getData('space'))->required()->col(8),
            FormBuilder::number('sort','排序',$room->getData('sort'))->col(8),
            FormBuilder::radio('breakfast','有无早餐',$room->getData('breakfast'))->options([['label'=>'无','value'=>0],['label'=>'有','value'=>1]])->required()->required()->col(8),
            FormBuilder::radio('preferential','超值优惠',$room->getData('preferential'))->options([['label'=>'否','value'=>0],['label'=>'是','value'=>1]])->required()->col(8),
            FormBuilder::radio('status','状态',$room->getData('status'))->options([['label'=>'未入住','value'=>0],['label'=>'已入住','value'=>1],['label'=>'禁用','value'=>2]])->required()->col(8)
        ];
        $form = FormBuilder::make_post_form('修改',$field,Url::build('update',['id'=>$id]),2);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    public function update(Request $request,$id)
    {
        $data = UtilService::postMore([
            ['type_id',0],
            ['number',0],
            ['title',''],
            ['bed',''],
            ['image',[]],
            ['price',0],
            ['space',0],
            ['original_price',0],
            ['give_integral',0],
            ['sort',0],
            ['breakfast',0],
            ['preferential',0],
            ['status',0]
        ],$request);
        if(!$data['type_id']) return JsonService::fail('请选择房间类型');
        if($data['number'] == '') return JsonService::fail('请输入房间号');
        if($data['title'] == '') return JsonService::fail('请输入房间标题');
        if($data['bed'] == '') return JsonService::fail('请输入床铺类型');
        if(!$data['space']) return JsonService::fail('请输入房间面积');
        if($data['image'][0] == '') return JsonService::fail('请上传图片');
        if(!$data['price']) return JsonService::fail('请输入房间价格');
        if($data['price'] > $data['original_price']) return JsonService::fail('房间价格不能大于原价');
        $room = StayRoomModel::get($id);

        if ($room['number'] != $data['number'] && StayRoomModel::be(['number'=>$data['number']])) return JsonService::fail('房间号重复');
        if ($data['status'] == 0 || $data['status'] == 1){
            $type = StayTypeModel::get($data['type_id']);
            if ($type['status'] == 0) return JsonService::fail('此类型房间已禁用,不能选择此状态');
        }
        if($data['sort'] < 0) $data['sort'] = 0;
        $data['image'] = $data['image'][0];
        $res = StayRoomModel::edit($data,$id);
        if ($res){
            return JsonService::successful('修改成功!');
        }else{
            return JsonService::fail('修改失败!');
        }
    }
}