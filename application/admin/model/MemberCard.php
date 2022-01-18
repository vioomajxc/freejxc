<?php
namespace app\admin\model;
use think\Model;
class MemberCard extends Model
{
    protected  $name = 'member_card';

    protected $autoWriteTimestamp = true;

    /**
     * 获取时间区间
     */
    public static function getTime($where,$model=null,$prefix='card_time'){
        if ($model == null) $model = new self;
        if(!$where['date']) return $model;
        if ($where['data'] == '') {
            $limitTimeList = [
                'today'=>implode(' - ',[date('Y/m/d'),date('Y/m/d',strtotime('+1 day'))]),
                'week'=>implode(' - ',[
                    date('Y/m/d', (time() - ((date('w') == 0 ? 7 : date('w')) - 1) * 24 * 3600)),
                    date('Y-m-d', (time() + (7 - (date('w') == 0 ? 7 : date('w'))) * 24 * 3600))
                ]),
                'month'=>implode(' - ',[date('Y/m').'/01',date('Y/m').'/'.date('t')]),
                'quarter'=>implode(' - ',[
                    date('Y').'/'.(ceil((date('n'))/3)*3-3+1).'/01',
                    date('Y').'/'.(ceil((date('n'))/3)*3).'/'.date('t',mktime(0,0,0,(ceil((date('n'))/3)*3),1,date('Y')))
                ]),
                'year'=>implode(' - ',[
                    date('Y').'/01/01',date('Y/m/d',strtotime(date('Y').'/01/01 + 1year -1 day'))
                ])
            ];
            $where['data'] = $limitTimeList[$where['date']];
        }
        list($startTime, $endTime) = explode(' - ', $where['data']);
        $model = $model->where($prefix, '>', strtotime($startTime));
        $model = $model->where($prefix, '<', strtotime($endTime));
        return $model;
    }

}