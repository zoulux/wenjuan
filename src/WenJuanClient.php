<?php

namespace Jake\Wenjuan;

class WenJuanClient
{
    const MODE_DEV = 'DEV';
    const MODE_PRO = 'PRO';

    const DOMAIN_DEV = 'http://apitest.wenjuan.com';
    const DOMAIN_PRO = 'https://www.wenjuan.com';

    private $appKey = '';
    private $appSecret = '';
    private $domain = self::DOMAIN_PRO;
    private $timestamp = '';

    /**
     * WenJuanClient constructor.
     * @param array $config
     */
    public function __construct($config)
    {

        if ($config['app_key'] ?? null) {
            $this->appKey = $config['app_key'];
        }

        if ($config['app_secret'] ?? null) {
            $this->appSecret = $config['app_secret'];
        }

        if ($mode = $config['mode'] ?? null) {
            switch ($mode) {
                case self::MODE_DEV:
                    $this->domain = self::DOMAIN_DEV;
                    break;
                case self::MODE_PRO:
                    $this->domain = self::DOMAIN_PRO;
                    break;
            }
        }

        if ($config['domain'] ?? null) {
            $this->domain = $config['domain'];
        }

        if ($config['timestamp'] ?? null) {
            $this->timestamp = $config['timestamp'];
        }
    }


    /**
     * 第三方登录
     * https://www.wenjuan.com/open/devdocument_v3_4_1
     * @param string $userId 用户编号
     * @param string $email
     * @param string $mobile
     * @return string
     */
    public function login($userId, $email = '', $mobile = '')
    {
        $params = [
            'wj_user' => $userId,
        ];

        if ($email) {
            $params['wj_email'] = $email;
        }

        if ($mobile) {
            $params['wj_mobile'] = $mobile;
        }

        $params = self::generateParams($params);
        return self::generateUrl('/openapi/v3/login', $params);
    }


    /**
     * 创建项目
     * https://www.wenjuan.com/open/devdocument_v3_4_2
     * @param string $userId 用户编号
     * @param string $ptype 项目类型(问卷：survey，表单：form，测评：assess，默认创建类型为survey)
     * @return string
     */
    public function projectCreate($userId, $ptype = '')
    {
        $params = [
            'wj_user' => $userId,
        ];

        if ($ptype) {
            $params['wj_ptype'] = $ptype;
        }

        $params = self::generateParams($params);
        return self::generateUrl('/openapi/v3/create_proj', $params);
    }

    /**
     * 编辑项目
     * https://www.wenjuan.com/open/devdocument_v3_4_2
     * @param string $userId 用户编号
     * @param string $shortId 项目短id
     * @param string $tostatus 0:"未发布", 1:"收集中", 2:"已结束", -2:"删除到回收站", -1:"永久删除"
     * @return string
     */
    public function projectUpdateStatus($userId, $shortId, $tostatus)
    {
        $params = [
            'wj_user' => $userId,
            'wj_short_id' => $shortId,
            'wj_tostatus' => $tostatus,
        ];

        $params = self::generateParams($params);
        return self::generateUrl('/openapi/v3/change_proj_status', $params);
    }

    /**
     * 复制项目
     * https://www.wenjuan.com/open/devdocument_v3_4_2
     * @param string $shortId 项目短id
     * @param string $fromUser 复制项目，复制项目的账号
     * @param string $title 复制新项目的标题，默认和原标题一样
     * @param string $toUser 目标账号，默认复制到问卷所有者名下
     * @return string
     */
    public function projectCopy($shortId, $fromUser, $title = '', $toUser = '')
    {
        $params = [
            'wj_short_id' => $shortId,
            'wj_from_user' => $fromUser,
        ];

        if ($title) {
            $params['wj_title'] = $title;
        }

        if ($toUser) {
            $params['wj_to_user'] = $toUser;
        }

        $params = self::generateParams($params);
        return self::generateUrl('/openapi/v3/copy_proj', $params);
    }


    /**
     * 获取项目状态
     * https://www.wenjuan.com/open/devdocument_v3_4_3
     * @param string $shortId 项目短id
     * @return string
     */
    public function projectStatus($shortId)
    {
        $params = [
            'wj_short_id' => $shortId,
        ];

        $params = self::generateParams($params);
        return self::generateUrl('/openapi/v3/get_proj_status', $params);
    }

    /**
     * 获取项目信息
     * https://www.wenjuan.com/open/devdocument_v3_4_3
     * @param string $shortId 项目短id
     * @return string
     */
    public function projectDetail($shortId)
    {
        $params = [
            'wj_short_id' => $shortId,
        ];

        $params = self::generateParams($params);
        return self::generateUrl('/openapi/v3/get_proj_detail', $params);
    }

    /**
     * 获取项目列表
     * https://www.wenjuan.com/open/devdocument_v3_4_3
     * 备注：
     * （1）如果请求不带wj_user参数，那么获取该appkey下的所有的项目； 如果带wj_user参数，获得该user创建的项目
     * （2）如果请求带wj_status参数，那么获取指定状态的项目，可以查询组合状态， 各状态之间以|分割，例： 0|1|2
     * @param string $user 用户编号，接入方用户的唯一标识
     * @param string $ptype 项目类型(form、survey或assess， 默认获取全部类型的项目)
     * @param string $page 查看第几页, 如果不带page参数，则返回所有项目列表
     * @param string $pagesize 每页包含多少条目,默认20条
     * @param int $status 项目状态；0:"未发布", 1:"收集中", 2:"已结束", 3:"暂停中", -2:"已删除,"-1:"永久删除
     * @return string
     */
    public function projectList($user = '', $ptype = '', $page = '', $pagesize = '', $status = null)
    {
        $params = [];

        if ($user) {
            $params['wj_user'] = $user;
        }

        if ($ptype) {
            $params['wj_ptype'] = $ptype;
        }

        if ($page) {
            $params['wj_page'] = $page;
        }

        if ($pagesize) {
            $params['wj_pagesize'] = $pagesize;
        }

        if (!is_null($status)) {
            $params['wj_status'] = $status;
        }

        $params = self::generateParams($params);
        return self::generateUrl('/openapi/v3/get_proj_list', $params);
    }


    /**
     * 获取答题链接
     * https://www.wenjuan.com/open/devdocument_v3_4_4
     * @param string $shortId 项目短id
     * @param string $respondent 答题者标识，由第三方开发者提供，查看数据详情时，对应source(来源)字段
     * @param string $repeat 同一答题者respondent可重复答题，wj_repeat=1代表可重复答题
     * @param string $callback 问卷网在答题结束时会向 callback 发起一个get请求
     * @param string $test 如果test=1, 那么为答卷预览链接，不可提交数据。
     * @return string
     */
    public function sheetGet($shortId, $respondent, $repeat = '', $callback = '', $test = '')
    {
        $params = [
            'wj_respondent' => $respondent,
        ];

        if ($test) {
            $params['test'] = $test;
        }

        if ($repeat) {
            $params['wj_repeat'] = $repeat;
        }

        if ($callback) {
            $params['wj_callback'] = $callback;
        }

        $params = self::generateParams($params);
        return self::generateUrl("/s/$shortId", $params);
    }


    /**
     * 获取数据分析结果
     * https://www.wenjuan.com/open/devdocument_v3_4_5
     * @param string $user 用户编号，接入方用户的唯一标识
     * @param string $shortId 项目短id
     * @return string
     */
    public function basicChartGet($user, $shortId)
    {
        $params = [
            'wj_user' => $user,
            'wj_short_id' => $shortId,
        ];

        $params = self::generateParams($params);
        return self::generateUrl('/openapi/v3/get_basic_chart', $params);
    }

    /**
     * 获取数据报表
     * https://www.wenjuan.com/open/devdocument_v3_4_5
     * @param string $user 用户编号，接入方用户的唯一标识
     * @param string $shortId 项目短id
     * @return string
     */
    public function reportChartGet($user, $shortId)
    {
        $params = [
            'wj_user' => $user,
            'wj_short_id' => $shortId,
        ];
        $params = self::generateParams($params);
        return self::generateUrl('/v3/get_report_chart', $params);
    }

    /**
     * 获取答卷详情列表
     * https://www.wenjuan.com/open/devdocument_v3_4_5
     * @param string $user 用户编号，接入方用户的唯一标识
     * @param string $shortId 项目短id
     * @param string $page 查看第几页, 如果不带page参数，默认第一页
     * @param string $pagesize 每页包含多少条目,默认20条
     * @return string
     */
    public function rspdDetailListGet($user, $shortId, $page = '', $pagesize = '')
    {
        $params = [
            'wj_user' => $user,
            'wj_short_id' => $shortId,
        ];
        if ($page) {
            $params['wj_page'] = $page;
        }
        if ($pagesize) {
            $params['wj_pagesize'] = $pagesize;
        }

        $params = self::generateParams($params);
        return self::generateUrl('/openapi/v3/get_rspd_detail_list', $params);
    }

    /**
     * 查看答题者最新一条答卷详情
     * https://www.wenjuan.com/open/devdocument_v3_4_5
     * @param string $user 用户编号，接入方用户的唯一标识
     * @param string $shortId 项目短id
     * @param string $respondent 答题者标识，由第三方开发者提供，查看数据详情时，对应source(来源)字段
     * @param string $datatype 返回数据类型(json或html, 默认html)
     * @return string
     */
    public function rspdDetailGet($user, $shortId, $respondent, $datatype = '')
    {
        $params = [
            'wj_user' => $user,
            'wj_short_id' => $shortId,
        ];
        if ($respondent) {
            $params['wj_respondent'] = $respondent;
        }
        if ($datatype) {
            $params['wj_datatype'] = $datatype;
        }

        $params = self::generateParams($params);
        return self::generateUrl('/openapi/v3/get_rspd_detail', $params);
    }


/////////////////////////////////////////////系统函数////////////////////////////////////////////////////////////////////

    public function generateUrl($path, $params)
    {
        return sprintf('%s%s?%s', $this->domain, $path, http_build_query($params));
    }

    /**
     * @param $params
     * @return array
     */
    public function generateParams($params)
    {
        $params['wj_appkey'] = $this->appKey;

        if ($this->timestamp) {
            $params['wj_timestamp'] = $this->timestamp;
        } else {
            $params['wj_timestamp'] = time();
        }
        $params['wj_signature'] = self::sign($params);
        return $params;
    }

    public function sign($params)
    {
        ksort($params);//将参数按key进行排序

        $values = array_values($params);
        $str = implode('', $values);

        $str .= $this->appSecret;

        return md5($str);//计算md5值;
    }

}