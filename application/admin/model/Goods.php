<?php
namespace app\admin\model;
use think\Model;
use service\PHPExcelService;
use think\Db;
use traits\ModelTrait;
use app\admin\model\Category as CategoryModel;
use app\admin\model\Order;
use app\admin\model\Item;
use app\admin\model\Stocks as StocksModel;

/**
 * 产品管理 model
 * Class StoreProduct
 * @package app\admin\model\store
 */
class Goods extends Model
{
	use ModelTrait;
    /**删除产品
     * @param $id
     */
    public static function proDelete($id){
//        //删除产品
//        //删除属性
//        //删除秒杀
//        //删除拼团
//        //删除砍价
//        //删除拼团
//        $model=new self();
//        self::beginTrans();
//        $res0 = $model::del($id);
//        $res1 = StoreSeckillModel::where(['product_id'=>$id])->delete();
//        $res2 = StoreCombinationModel::where(['product_id'=>$id])->delete();
//        $res3 = StoreBargainModel::where(['product_id'=>$id])->delete();
//        //。。。。
//        $res = $res0 && $res1 && $res2 && $res3;
//        self::checkTrans($res);
//        return $res;
    }
    /**
     * 获取连表查询条件
     * @param $type
     * @return array
     */
    public static function setData($type){
        switch ((int)$type){
            case 1:
                $data = ['p.is_show'=>1,'p.is_del'=>0];
                break;
            case 2:
                $data = ['p.is_show'=>0,'p.is_del'=>0];
                break;
            case 3:
                $data = ['p.is_del'=>0];
                break;
            case 4:
                $data = ['p.is_show'=>1,'p.is_del'=>0,'pav.stock|p.stock'=>0];
                break;
            case 5:
                $min = SystemConfig::getValue('store_stock');
                $data = ['p.is_show'=>1,'p.is_del'=>0,'pav.stock|p.stock'=>['elt',$min]];
                break;
            case 6:
                $data = ['p.is_del'=>1];
                break;
        };
        return isset($data) ? $data: [];
    }
    /**
     * 获取连表MOdel
     * @param $model
     * @return object
     */
    public static function getModelObject($where=[]){
        $model=new self();
        $model=$model->alias('p')->join('StoreProductAttrValue pav','p.id=pav.product_id','LEFT');
        if(!empty($where)){
            $model=$model->group('p.id');
            if(isset($where['type']) && $where['type']!='' && ($data=self::setData($where['type']))){
                $model = $model->where($data);
            }
            if(isset($where['store_name']) && $where['store_name']!=''){
                $model = $model->where('p.store_name|p.keyword|p.id','LIKE',"%$where[store_name]%");
            }
            if(isset($where['cate_id']) && trim($where['cate_id'])!=''){
                $catid1 = $where['cate_id'].',';//匹配最前面的cateid
                $catid2 = ','.$where['cate_id'].',';//匹配中间的cateid
                $catid3 = ','.$where['cate_id'];//匹配后面的cateid
                $catid4 = $where['cate_id'];//匹配全等的cateid
//                $model = $model->whereOr('p.cate_id','LIKE',["%$catid%",$catidab]);
                $sql = " LIKE '$catid1%' OR `cate_id` LIKE '%$catid2%' OR `cate_id` LIKE '%$catid3' OR `cate_id`=$catid4";
                $model->where(self::getPidSql($where['cate_id']));
            }
            if(isset($where['order']) && $where['order']!=''){
                $model = $model->order(self::setOrder($where['order']));
            }
        }
        return $model;
    }

    /**根据cateid查询产品 拼sql语句
     * @param $cateid
     * @return string
     */
    protected static function getCateSql($cateid){
        $lcateid = $cateid.',%';//匹配最前面的cateid
        $ccatid = '%,'.$cateid.',%';//匹配中间的cateid
        $ratidid = '%,'.$cateid;//匹配后面的cateid
        return  " `cate_id` LIKE '$lcateid' OR `cate_id` LIKE '$ccatid' OR `cate_id` LIKE '$ratidid' OR `cate_id`=$cateid";
    }

    /** 如果有子分类查询子分类获取拼接查询sql
     * @param $cateid
     * @return string
     */
    protected static function getPidSql($cateid){

        $sql = self::getCateSql($cateid);
        $ids = CategoryModel::where('pid', $cateid)->column('id');
        //查询如果有子分类获取子分类查询sql语句
        if($ids) foreach ($ids as $v) $sql .= " OR ".self::getcatesql($v);
        return $sql;
    }
    /*
     * 获取产品列表
     * @param $where array
     * @return array
     *
     */
    public static function ProductList($where){
        $model=self::getModelObject($where)->field(['p.*','sum(pav.stock) as vstock']);
        if($where['excel']==0) $model=$model->page((int)$where['page'],(int)$where['limit']);
        $data=($data=$model->select()) && count($data) ? $data->toArray():[];
        foreach ($data as &$item){
            $cateName = CategoryModel::where('id', 'IN', $item['cate_id'])->column('cate_name', 'id');
            $item['cate_name']=is_array($cateName) ? implode(',',$cateName) : '';
            $item['collect'] = StoreProductRelation::where('product_id',$item['id'])->where('type','collect')->count();//收藏
            $item['like'] = StoreProductRelation::where('product_id',$item['id'])->where('type','like')->count();//点赞
            $item['stock'] = self::getStock($item['id'])>0?self::getStock($item['id']):$item['stock'];//库存
            $item['stock_attr'] = self::getStock($item['id'])>0 ? true : false;//库存
            $item['sales_attr'] = self::getSales($item['id']);//属性销量
            $item['visitor'] = Db::name('store_visit')->where('product_id',$item['id'])->where('product_type','product')->count();
        }
        if($where['excel']==1){
            $export = [];
            foreach ($data as $index=>$item){
                $export[] = [
                    $item['store_name'],
                    $item['store_info'],
                    $item['cate_name'],
                    '￥'.$item['price'],
                    $item['stock'],
                    $item['sales'],
                    $item['like'],
                    $item['collect']
                ];
            }
            PHPExcelService::setExcelHeader(['产品名称','产品简介','产品分类','价格','库存','销量','点赞人数','收藏人数'])
                ->setExcelTile('产品导出','产品信息'.time(),' 生成时间：'.date('Y-m-d H:i:s',time()))
                ->setExcelContent($export)
                ->ExcelSave();
        }
        $count=self::getModelObject($where)->count();
        return compact('count','data');
    }

    //商品统计图表
    public static function getChatrdata($type,$data){
        $legdata=['金额','销量'];
        $model=self::setWhereType(self::order('create_time desc'),$type);
        $list=self::getModelTime(compact('data'),$model)->alias('g')->join('item i','g.id=i.gd_id')->join('order o','o.or_id=i.or_id')->where("o.or_verify_status=1 and (o.or_type='pos' or o.or_type='sales')")
            ->field('FROM_UNIXTIME(or_create_time,"%Y-%c-%d") as un_time,sum(it_number) as count,sum(it_number*it_price) as sales')
            ->group('un_time')
            ->distinct(true)
            ->select()
            ->toArray();
        $chatrList=[];
        $datetime=[];
        $data_item=[];
        $itemList=[0=>[],1=>[]];
        foreach ($list as $item){
            $itemList[0][]=round($item['sales'],2);
            $itemList[1][]=$item['count'];
            array_push($datetime,$item['un_time']);//往数组尾部添加
            $Edatetime = sort($datetime);
        }
        foreach ($legdata as $key=>$leg){
            $data_item['name']=$leg;
            $data_item['type']='line';
            $data_item['data']=$itemList[$key];
            $chatrList[]=$data_item;
            unset($data_item);
        }
        unset($leg);//销毁$leg
        $badge=self::getbadge(compact('data'),$type);//compact('data'),$type
        $count=self::setWhereType(self::getModelTime(compact('data'),new self()),$type)->count();
        return compact('datetime','chatrList','legdata','badge','count');

    }
    //获取 badge 内容
    public static function getbadge($where,$type){
    	$rep_count=self::getModelTime($where,new self())->alias('g')->join('Stocks s','g.id=s.goods_id')->where('g.replenishment','>','s.numbers')->field('replenishment,numbers')->count();
        $sum_stock=self::alias('g')->join('stocks s','g.id=s.goods_id')->where('replenishment','>','s.numbers')->sum('numbers');
        
        return [
            [
                'name'=>'商品种类',
                'field'=>'件',
                'count'=>self::setWhereType(new self(),$type)->where('create_time','<',mktime(0,0,0,date('m'),date('d'),date('Y')))->count(),//self::Distinct(true)->count('category'),
                'content'=>'商品数量总数',
                'background_color'=>'layui-bg-blue',
                'sum'=>self::count(),
                'class'=>'fa fa fa-ioxhost',
            ],
            [
                'name'=>'新增商品',
                'field'=>'件',
                'count'=>self::setWhereType(self::getModelTime($where,new self),$type)->alias('g')->join('item i','g.id=i.gd_id')->join('order o','i.or_id=o.or_id')->where('o.or_type','procure')->count('i.gd_id'),//StocksModel::sum('numbers'),
                'content'=>'新增商品总数',
                'background_color'=>'layui-bg-cyan',
                'sum'=>self::alias('g')->join('item i','g.id=i.gd_id')->join('order o','i.or_id=o.or_id')->where('o.or_type','procure')->sum('i.it_number'),
                'class'=>'fa fa-line-chart',
            ],
            [
                'name'=>'活动商品',
                'field'=>'件',
                'count'=>self::setWhereType(self::getModelTime($where,new self),$type)->alias('g')->join('item i','g.id=i.gd_id')->join('order o','i.or_id=o.or_id')->where("o.or_type='pos' or o.or_type='sales'")->count('i.gd_id'),
                'content'=>'活动商品总数',
                'background_color'=>'layui-bg-green',
                'sum'=>self::alias('g')->join('item i','g.id=i.gd_id')->join('order o','i.or_id=o.or_id')->where('o.or_type','procure')->sum('i.it_number'),
                'class'=>'fa fa-bar-chart',
            ],
            [
                'name'=>'缺货商品',
                'field'=>'件',
                'count'=>$rep_count,
                'content'=>'总商品数量',
                'background_color'=>'layui-bg-orange',
                'sum'=>$sum_stock,
                'class'=>'fa fa-cube',
            ],
        ];
    }
    public static function setWhereType($model,$type){
        switch ($type){
            case 1:
                $data = ['status'=>1];
                break;
            case 2:
                $data = ['is_show'=>0,'is_del'=>0];
                break;
            case 3:
                $data = ['is_del'=>0];
                break;
            case 4:
                $data = ['is_show'=>1,'is_del'=>0,'stock'=>0];
                break;
            case 5:
                $data = ['is_show'=>1,'is_del'=>0,'stock'=>['elt',1]];
                break;
            case 6:
                $data = ['is_del'=>1];
                break;
                default:
                $data = ['status'=>1];
                break;
        }
        if(isset($data)) $model = $model->where($data);
        return $model;
    }
    /*
     * layui-bg-red 红 layui-bg-orange 黄 layui-bg-green 绿 layui-bg-blue 蓝 layui-bg-cyan 黑
     * 销量排行 top 10
     */
    public static function getMaxList($where){
        $classs=['layui-bg-red','layui-bg-orange','layui-bg-green','layui-bg-blue','layui-bg-cyan'];
        $model=Order::alias('o')->join('Item i','o.or_id=i.or_id')->join('goods g','i.gd_id=g.id');
        $list=self::getModelTime($where,$model,'o.or_create_time')->group('i.gd_id')->where("or_verify_status=1 and (or_type='pos' or or_type='sales')")->limit(10)
            ->field(['sum(i.it_number) as p_count','g.goodsname','sum(it_number*it_price) as sum_price'])->order('p_count desc')->select();
        if(count($list)) $list=$list->toArray();
        $maxList=[];
        $sum_count=0;
        $sum_price=0;
        foreach ($list as $item){
            $sum_count+=$item['p_count'];
            $sum_price=bcadd($sum_price,$item['sum_price'],2);//求和后保留
        }
        unset($item);
        foreach ($list as $key=>&$item){
            $item['w']=bcdiv($item['p_count'],$sum_count,2)*100;//左除右
            $item['class']=isset($classs[$key]) ?$classs[$key]:( isset($classs[$key-count($classs)]) ? $classs[$key-count($classs)]:'');
            $item['store_name']=self::getSubstrUTf8($item['goodsname']);
        }
        $maxList['sum_count']=$sum_count;
        $maxList['sum_price']=$sum_price;
        $maxList['list']=$list;
        return $maxList;
    }
    //获取利润
    public static function ProfityTop10($where){
        $classs=['layui-bg-red','layui-bg-orange','layui-bg-green','layui-bg-blue','layui-bg-cyan'];
        $model=Order::alias('o')->join('Item i','o.or_id=i.or_id')->join('Goods g','i.gd_id=g.id');
        $list=self::getModelTime($where,$model,'o.or_create_time')->group('i.gd_id')->order('sum_price desc')->limit(10)
        ->where(['o.or_verify_status'=>1,'o.or_type'=>'sales'])
        ->whereOr('o.or_type','pos')
        ->field(['sum(i.it_number) as p_count','g.goodsname','sum(it_number*(it_price-g.cost)) as sum_price','(g.price-g.cost) as profity'])
            ->select();
        if(count($list)) $list=$list->toArray();
        $maxList=[];
        $sum_count=0;
        $sum_price=0;
        foreach ($list as $item){
            $sum_count+=$item['p_count'];
            $sum_price=bcadd($sum_price,$item['sum_price'],2);
        }
        foreach ($list as $key=>&$item){
            $item['w']=bcdiv($item['sum_price'],$sum_price,4)*100;
            $item['class']=isset($classs[$key]) ?$classs[$key]:( isset($classs[$key-count($classs)]) ? $classs[$key-count($classs)]:'');
            $item['store_name']=self::getSubstrUTf8($item['goodsname'],30);
            $item['sum_price'] = round($item['sum_price'],2);
        }
        $maxList['sum_count']=$sum_count;
        $maxList['sum_price']=$sum_price;
        $maxList['list']=$list;
        return $maxList;
    }
    //获取缺货
    public static function getLackList($where){
    	$model=StocksModel::alias('s')->join('Goods g','s.goods_id=g.id');
        $list=self::getModelTime($where,$model,'g.create_time')->where('g.replenishment','>','s.numbers')->field(['g.id','goodsname','numbers','price','replenishment'])->page((int)$where['page'],(int)$where['limit'])->order('numbers asc')->select();
        if(count($list)) $list=$list->toArray();
        $count=self::alias('g')->join('Stocks s','g.id=s.goods_id')->where('g.replenishment','>','s.numbers')->count();
        return ['count'=>$count,'data'=>$list];
    }
    //获取差评
    public static function getnegativelist($where){
        $list=self::alias('s')->join('StoreProductReply r','s.id=r.product_id')
            ->field('s.id,s.store_name,s.price,count(r.product_id) as count')
            ->page((int)$where['page'],(int)$where['limit'])
            ->where('r.product_score',1)
            ->order('count desc')
            ->group('r.product_id')
            ->select();
        if(count($list)) $list=$list->toArray();
        $count=self::alias('s')->join('StoreProductReply r','s.id=r.product_id')->group('r.product_id')->where('r.product_score',1)->count();
        return ['count'=>$count,'data'=>$list];
    }
    public static function TuiProductList(){
        $perd=Item::alias('i')->join('order o','i.or_id=o.or_id')->join('goods g','i.gd_id=g.id')
            ->field('sum(i.it_number) as count,i.gd_id as id')
            ->group('i.gd_id')
            ->where(['o.or_type'=>'sales_return','o.or_verify_status'=>1])
            ->order('count desc')
            ->limit(10)
            ->select();
        if(count($perd)) $perd=$perd->toArray();
        foreach ($perd as &$item){
            $item['store_name']=self::where(['id'=>$item['id']])->value('goodsname');
            $item['price']=self::where(['id'=>$item['id']])->value('price');
        }
        return $perd;
        var_dump($perd);
    }
    //编辑库存
    public static function changeStock($stock,$productId)
    {
        return self::edit(compact('stock'),$productId);
    }
    //获取库存数量
    public static function getStock($productId)
    {
        return StoreProductAttrValue::where(['product_id'=>$productId])->sum('stock');
    }
    //获取总销量
    public static function getSales($productId)
    {
        return StoreProductAttrValue::where(['product_id'=>$productId])->sum('sales');
    }

    public static function getTierList($model = null)
    {
        if($model === null) $model = new self();
        return $model->field('id,store_name')->where('is_del',0)->select()->toArray();
    }
    /**
     * 设置查询条件
     * @param array $where
     * @return array
     */
    public static function setWhere($where){
        $time['data']='';
        if(isset($where['start_time']) && $where['start_time']!='' && isset($where['end_time']) && $where['end_time']!=''){
            $time['data']=$where['start_time'].' - '.$where['end_time'];
        }else{
            $time['data']=isset($where['data'])? $where['data']:'';
        }
        $model=self::getModelTime($time, Db::name('store_cart')->alias('a')->join('__STORE_PRODUCT__ b','a.product_id=b.id'),'a.add_time');
        if(isset($where['title']) && $where['title']!=''){
            $model=$model->where('b.store_name|b.id','like',"%$where[title]%");
        }
        return $model;
    }
    /**
     * 获取真实销量排行
     * @param array $where
     * @return array
     */
    public static function getSaleslists($where){
        $data=self::setWhere($where)->where('a.is_pay',1)
            ->group('a.product_id')
            ->field(['sum(a.cart_num) as num_product','b.store_name','b.image','b.price','b.id'])
            ->order('num_product desc')
            ->page((int)$where['page'],(int)$where['limit'])
            ->select();
        $count=self::setWhere($where)->where('a.is_pay',1)->group('a.product_id')->count();
        foreach ($data as &$item){
            $item['sum_price']=bcmul($item['num_product'],$item['price'],2);
        }
        return compact('data','count');
    }
    public static function SaveProductExport($where){
        $list=self::setWhere($where)
            ->where('a.is_pay',1)
            ->field(['sum(a.cart_num) as num_product','b.store_name','b.image','b.price','b.id'])
            ->order('num_product desc')
            ->group('a.product_id')
            ->select();
        $export=[];
        foreach ($list as $item){
            $export[]=[
                $item['id'],
                $item['store_name'],
                $item['price'],
                bcmul($item['num_product'],$item['price'],2),
                $item['num_product'],
            ];
        }
        PHPExcelService::setExcelHeader(['商品编号','商品名称','商品售价','销售额','销量'])
            ->setExcelTile('产品销量排行','产品销量排行',' 生成时间：'.date('Y-m-d H:i:s',time()))
            ->setExcelContent($export)
            ->ExcelSave();
    }
    /*
     *  单个商品详情的头部查询
     *  $id 商品id
     *  $where 条件
     */
    public static function getProductBadgeList($id,$where){
        $data['data']=$where;
        $list=self::setWhere($data)
            ->field(['sum(a.cart_num) as num_product','b.id','b.price'])
            ->where('a.is_pay',1)
            ->group('a.product_id')
            ->order('num_product desc')
            ->select();
        //排名
        $ranking=0;
        //销量
        $xiaoliang=0;
        //销售额 数组
        $list_price=[];
        foreach ($list as $key=>$item){
            if($item['id']==$id){
                $ranking=$key+1;
                $xiaoliang=$item['num_product'];
            }
            $value['sum_price']=$item['price']*$item['num_product'];
            $value['id']=$item['id'];
            $list_price[]=$value;
        }
        //排序
        $list_price=self::my_sort($list_price,'sum_price',SORT_DESC);
        //销售额排名
        $rank_price=0;
        //当前销售额
        $num_price=0;
        if($list_price!==false && is_array($list_price)){
            foreach ($list_price as $key=>$item){
                if($item['id']==$id){
                    $num_price=$item['sum_price'];
                    $rank_price=$key+1;
                    continue;
                }
            }
        }
        return [
            [
                'name'=>'销售额排名',
                'field'=>'名',
                'count'=>$rank_price,
                'background_color'=>'layui-bg-blue',
            ],
            [
                'name'=>'销量排名',
                'field'=>'名',
                'count'=>$ranking,
                'background_color'=>'layui-bg-blue',
            ],
            [
                'name'=>'商品销量',
                'field'=>'名',
                'count'=>$xiaoliang,
                'background_color'=>'layui-bg-blue',
            ],
            [
                'name'=>'点赞次数',
                'field'=>'个',
                'count'=>Db::name('store_product_relation')->where('product_id',$id)->where('type','like')->count(),
                'background_color'=>'layui-bg-blue',
            ],
            [
                'name'=>'销售总额',
                'field'=>'元',
                'count'=>$num_price,
                'background_color'=>'layui-bg-blue',
                'col'=>12,
            ],
        ];
    }
    /*
     * 处理二维数组排序
     * $arrays 需要处理的数组
     * $sort_key 需要处理的key名
     * $sort_order 排序方式
     * $sort_type 类型 可不填写
     */
    public static function my_sort($arrays,$sort_key,$sort_order=SORT_ASC,$sort_type=SORT_NUMERIC ){
        if(is_array($arrays)){
            foreach ($arrays as $array){
                if(is_array($array)){
                    $key_arrays[] = $array[$sort_key];
                }else{
                    return false;
                }
            }
        }
        if(isset($key_arrays)){
            array_multisort($key_arrays,$sort_order,$sort_type,$arrays);
            return $arrays;
        }
        return false;
    }
    /*
     * 查询单个商品的销量曲线图
     *
     */
    public static function getProductCurve($where){
        $list=self::setWhere($where)
            ->where('a.product_id',$where['id'])
            ->where('a.is_pay',1)
            ->field(['FROM_UNIXTIME(a.add_time,"%Y-%m-%d") as _add_time','sum(a.cart_num) as num'])
            ->group('_add_time')
            ->order('_add_time asc')
            ->select();
        $seriesdata=[];
        $date=[];
        $zoom='';
        foreach ($list as $item){
            $date[]=$item['_add_time'];
            $seriesdata[]=$item['num'];
        }
        if(count($date)>$where['limit']) $zoom=$date[$where['limit']-5];
        return compact('seriesdata','date','zoom');
    }
    /*
     * 查询单个商品的销售列表
     *
     */
    public static function getSalelList($where){
        return self::setWhere($where)
            ->where(['a.product_id'=>$where['id'],'a.is_pay'=>1])
            ->join('user c','c.uid=a.uid')
            ->field(['FROM_UNIXTIME(a.add_time,"%Y-%m-%d") as _add_time','c.nickname','b.price','a.id','a.cart_num as num'])
            ->page((int)$where['page'],(int)$where['limit'])
            ->select();
    }

    /**
     * TODO 获取某个字段值
     * @param $id
     * @param string $field
     * @return mixed
     */
    public static function getProductField($id,$field = 'store_name'){
        return self::where('id',$id)->value($field);
    }
}