/*
Navicat MySQL Data Transfer

Source Server         : 本地
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : lotus_admin

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2019-07-28 16:44:51
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for lotus_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `lotus_auth_group`;
CREATE TABLE `lotus_auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(100) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `rules` text NOT NULL COMMENT '权限规则ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='权限组表';

-- ----------------------------
-- Records of lotus_auth_group
-- ----------------------------
INSERT INTO `lotus_auth_group` VALUES ('2', '演示用户', '1', '1,2,223,224,225,3,220,221,222,4,219,226,228,238,229,240,241,242,243,244,245,246,247,248,249,250,251,252,253,254,255,256,257,258,259,260,261,262,263,264,265,266,267,268,269,270,271,272,273,274,275,276,278,279,280,281,282,283,284');

-- ----------------------------
-- Table structure for lotus_auth_group_access
-- ----------------------------
DROP TABLE IF EXISTS `lotus_auth_group_access`;
CREATE TABLE `lotus_auth_group_access` (
  `uid` mediumint(8) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='权限组规则表';

-- ----------------------------
-- Records of lotus_auth_group_access
-- ----------------------------
INSERT INTO `lotus_auth_group_access` VALUES ('1', '1');

-- ----------------------------
-- Table structure for lotus_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `lotus_auth_rule`;
CREATE TABLE `lotus_auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL DEFAULT '' COMMENT '规则名称',
  `title` varchar(20) NOT NULL,
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `pid` smallint(5) unsigned NOT NULL COMMENT '父级ID',
  `icon` varchar(50) DEFAULT '' COMMENT '图标',
  `sort` int(50) unsigned NOT NULL COMMENT '排序',
  `condition` char(100) DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='规则表';

-- ----------------------------
-- Records of lotus_auth_rule
-- ----------------------------
INSERT INTO `lotus_auth_rule` VALUES (1, 'admin/user/default', '用户权限', 1, 1, 0, 'layui-icon-user', 8, '');
INSERT INTO `lotus_auth_rule` VALUES (2, 'admin/user/userList', '用户管理', 1, 1, 1, '', 1, '');
INSERT INTO `lotus_auth_rule` VALUES (3, 'admin/user/ruleList', '权限管理', 1, 1, 1, '', 1, '');
INSERT INTO `lotus_auth_rule` VALUES (4, 'admin/user/roleList', '角色管理', 1, 1, 1, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (219, 'admin/user/addRole', '新增角色', 1, 0, 4, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (220, 'admin/user/addRule', '新增权限', 1, 0, 3, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (221, 'admin/user/editRule', '编辑权限', 1, 0, 3, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (222, 'admin/user/deleteRule', '删除权限', 1, 0, 3, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (223, 'admin/User/addUser', '增加用户', 1, 0, 2, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (224, 'admin/user/editUser', '编辑用户', 1, 0, 2, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (225, 'admin/user/deleteUser', '删除用户', 1, 0, 2, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (226, 'admin/user/editRole', '角色授权', 1, 0, 4, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (238, 'admin/user/editpasswd', '修改密码', 1, 0, 0, '', 3, '');
INSERT INTO `lotus_auth_rule` VALUES (228, 'admin/user/delRole', '删除角色', 1, 0, 4, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (229, 'admin/userLog/index', '系统日志', 1, 1, 0, 'layui-icon-log', 9, '');
INSERT INTO `lotus_auth_rule` VALUES (239, 'admin/systemConfig/set', '系统设置', 1, 1, 0, 'layui-icon-util', 10, '');
INSERT INTO `lotus_auth_rule` VALUES (240, 'admin/basic/default', '基础数据', 1, 1, 0, 'layui-icon-set-sm', 7, '');
INSERT INTO `lotus_auth_rule` VALUES (241, 'admin/goods/goodsList', '商品管理', 1, 1, 240, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (242, 'admin/goods/addGoods', '增加商品', 1, 0, 241, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (243, 'admin/goods/editGoods', '编辑商品', 1, 0, 241, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (244, 'admin/goods/delGoods', '删除商品', 1, 0, 241, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (245, 'admin/goods/categoryList', '商品分类', 1, 1, 240, 'layui-icon-component', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (246, 'admin/goods/addCategory', '增加分类', 1, 0, 245, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (247, 'admin/goods/editCategory', '编辑分类', 1, 0, 245, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (248, 'admin/goods/delCategory', '删除分类', 1, 0, 245, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (249, 'admin/goods/goodsListJson', '商品列表', 1, 0, 241, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (250, 'admin/goods/categoryListJson', '分类列表', 1, 0, 245, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (251, 'admin/goods/desGoods', '商品详情', 1, 0, 241, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (252, 'admin/storage/default', '采购管理', 1, 1, 0, 'layui-icon-component', 5, '');
INSERT INTO `lotus_auth_rule` VALUES (253, 'admin/stocks/stocksList', '库存列表', 1, 1, 293, '', 8, '');
INSERT INTO `lotus_auth_rule` VALUES (254, 'admin/goods/storehouseList', '仓库管理', 1, 1, 240, '', 10, '');
INSERT INTO `lotus_auth_rule` VALUES (255, 'admin/goods/supplierList', '供应商管理', 1, 1, 240, '', 9, '');
INSERT INTO `lotus_auth_rule` VALUES (256, 'admin/storage/storageList', '采购订单', 1, 1, 252, '', 6, '');
INSERT INTO `lotus_auth_rule` VALUES (257, 'admin/storage/storageListJson', '商品入库列表', 1, 0, 256, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (258, 'admin/storage/returnNew', '采购退货', 1, 1, 252, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (259, 'admin/storage/returnList', '商品退货列表', 1, 0, 258, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (260, 'admin/goods/addStorehouse', '添加仓库', 1, 0, 254, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (261, 'admin/goods/editStorehouse', '编辑仓库', 1, 0, 254, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (262, 'admin/goods/delStorehouse', '删除仓库', 1, 0, 254, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (263, 'admin/goods/storehouseListJson', '仓库列表', 1, 0, 254, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (264, 'admin/goods/addSupplier', '添加供应商', 1, 0, 255, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (265, 'admin/goods/editSupplier', '编辑供应商', 1, 0, 255, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (266, 'admin/goods/delSupplier', '删除供应商', 1, 0, 255, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (267, 'admin/goods/supplierListJson', '供应商列表', 1, 0, 255, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (268, 'admin/stocks/stocksListJson', '库存数据读取', 1, 0, 253, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (269, 'admin/storage/addStorage', '新建采购', 1, 1, 252, '', 7, '');
INSERT INTO `lotus_auth_rule` VALUES (270, 'admin/goods/shopList', '门店管理', 1, 1, 240, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (271, 'admin/goods/shopListJson', '门店列表', 1, 0, 270, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (272, 'admin/goods/addShop', '添加门店', 1, 0, 270, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (273, 'admin/goods/editShop', '编辑门店', 1, 0, 270, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (274, 'admin/goods/delShop', '删除门店', 1, 0, 270, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (275, 'admin/member/memberList', '会员列表', 1, 1, 285, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (276, 'admin/member/memberListJson', '会员列表数据', 1, 0, 275, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (277, 'admin/member/addMember', '添加会员', 1, 0, 275, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (278, 'admin/member/editMember', '编辑会员', 1, 0, 275, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (279, 'admin/member/delMember', '删除会员', 1, 0, 275, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (280, 'admin/member/mcategoryList', '会员分类', 1, 1, 285, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (281, 'admin/member/mcategoryListJson', '会员分类列表', 1, 0, 280, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (282, 'admin/member/addMcategory', '添加会员分类', 1, 0, 280, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (283, 'admin/member/editMcategory', '编辑会员分类', 1, 0, 280, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (284, 'admin/member/delMcategory', '删除会员分类', 1, 0, 280, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (285, 'admin/member/memberDefault', '会员管理', 1, 1, 0, 'layui-icon-group', 6, '');
INSERT INTO `lotus_auth_rule` VALUES (286, 'admin/storage/returnNewList', '采购退货列表', 1, 1, 252, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (291, 'admin/member/addMemberCard', '会员开卡', 1, 0, 275, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (292, 'admin/storage/returnnewListJson', '读取采购退货列表', 1, 0, 258, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (293, 'admin/stocks/default', '库存管理', 1, 1, 0, 'layui-icon-form', 4, '');
INSERT INTO `lotus_auth_rule` VALUES (294, 'admin/stocks/stockstake', '新建盘点', 1, 1, 293, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (295, 'admin/stocks/stockstakeList', '历史盘点', 1, 1, 293, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (296, 'admin/stocks/stockstakeListJson', '读取历史盘点', 1, 0, 295, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (297, 'admin/stocks/allot', '新建调拔', 1, 1, 293, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (298, 'admin/stocks/allotList', '调拔列表', 1, 1, 293, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (299, 'admin/stocks/allotListJson', '读取调拔列表', 1, 0, 298, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (300, 'admin/sales/default', '销售管理', 1, 1, 0, 'layui-icon-rmb', 3, '');
INSERT INTO `lotus_auth_rule` VALUES (301, 'admin/sales/addSales', '新建销售', 1, 1, 300, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (302, 'admin/sales/salesList', '销售列表', 1, 1, 300, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (303, 'admin/sales/salesListJson', '读取销售列表', 1, 0, 302, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (304, 'admin/sales/addReturn', '新建退货', 1, 1, 300, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (305, 'admin/sales/returnList', '销售退货列表', 1, 1, 300, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (306, 'admin/returnListJson', '读取退货列表', 1, 0, 305, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (307, 'admin/financial/default', '财务管理', 1, 1, 0, 'layui-icon-picture-fine', 2, '');
INSERT INTO `lotus_auth_rule` VALUES (308, 'admin/financial/collectionList', '应收管理', 1, 1, 307, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (309, 'admin/financial/collectionListJson', '读取应收列表', 1, 0, 308, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (310, 'admin/financial/payList', '应付管理', 1, 1, 307, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (311, 'admin/financial/payListJson', '读取应付列表', 1, 0, 310, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (312, 'admin/financial/list', '帐务明细', 1, 1, 307, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (313, 'admin/financial/listJson', '读取帐务明细', 1, 0, 312, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (314, 'admin/report/default', '统计报表', 1, 1, 0, 'layui-icon-chart-screen', 1, '');
INSERT INTO `lotus_auth_rule` VALUES (315, 'admin/report/goods', '商品统计', 1, 1, 314, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (316, 'admin/report/procure', '采购统计', 1, 1, 314, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (317, 'admin/report/sales', '销售统计', 1, 1, 314, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (320, 'admin/stocks/increaseList', '增溢列表', 1, 1, 293, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (321, 'admin/stocks/increaseListJson', '读取增溢列表', 1, 0, 320, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (322, 'admin/stocks/addIncrease', '新建增溢', 1, 0, 320, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (323, 'admin/stocks/decreaseList', '损耗列表', 1, 1, 293, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (324, 'admin/stocks/decreaseListJson', '读取损耗列表', 1, 0, 323, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (325, 'admin/stocks/deIncrease', '新建损耗', 1, 0, 323, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (326, 'admin/index/orderchart', '欢迎页订单统计', 1, 0, 314, '', 0, '');
INSERT INTO `lotus_auth_rule` VALUES (327, 'admin/index/memberchart', '欢迎页会员统计', 1, 0, 314, '', 0, '');

-- ----------------------------
-- Table structure for lotus_system
-- ----------------------------
DROP TABLE IF EXISTS `lotus_system`;
CREATE TABLE `lotus_system` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '配置项名称',
  `value` text NOT NULL COMMENT '配置项值',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='系统配置表';

-- ----------------------------
-- Records of lotus_system
-- ----------------------------
INSERT INTO `lotus_system` VALUES ('1', 'site_config', 'a:13:{s:9:\"site_name\";s:24:\"唯马收银管理系统\";s:11:\"login_title\";s:18:\"唯马收银系统\";s:5:\"admin\";s:9:\"马洪利\";s:5:\"phone\";s:11:\"15802591693\";s:7:\"address\";s:52:\"湖南省长沙市长沙县开元路星隆国际2918\";s:5:\"email\";s:15:\"12612019@qq.com\";s:12:\"wechat_mchid\";s:3:\"无\";s:12:\"wechat_appid\";s:3:\"无\";s:10:\"wechat_key\";s:3:\"无\";s:16:\"wechat_appsecret\";s:3:\"无\";s:12:\"alipay_appid\";s:3:\"无\";s:17:\"alipay_public_key\";s:3:\"无\";s:18:\"alipay_private_key\";s:3:\"无\";}');

-- ----------------------------
-- Table structure for lotus_user
-- ----------------------------
DROP TABLE IF EXISTS `lotus_user`;
CREATE TABLE `lotus_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL COMMENT '用户名',
  `password` varchar(50) NOT NULL COMMENT '密码',
  `mobile` varchar(11) DEFAULT '' COMMENT '手机',
  `email` varchar(50) DEFAULT '' COMMENT '邮箱',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '用户状态  1 正常  2 禁止',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `last_login_time` datetime DEFAULT NULL COMMENT '最后登陆时间',
  `last_login_ip` varchar(50) DEFAULT '' COMMENT '最后登录IP',
  `shop` smallint(2) DEFAULT '0' NULL COMMENT '所属店铺',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------------------
-- Table structure for table `viooma_user_ext`
-- ----------------------------------------
DROP TABLE IF EXISTS `lotus_user_ext`;
CREATE TABLE `lotus_user_ext` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `userid` int(4) NOT NULL COMMENT '用户ID',
  `fullname` varchar(10) NULL COMMENT '全名',
  `sex` varchar(2) NULL COMMENT '性别',
  `birthday` int(11) DEFAULT NULL COMMENT '生日',
  `card` varchar(18) NULL COMMENT '身份证号',
  `address` varchar(200) DEFAULT NULL COMMENT '地址',
  `depart` varchar(50) DEFAULT NULL COMMENT '部门',
  `position` varchar(50) DEFAULT NULL COMMENT '职位',
  `telphone` varchar(11) DEFAULT NULL COMMENT '手机',
  `im` varchar(20) DEFAULT NULL COMMENT '即时通讯',
  `salary` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '月薪',
  `jointime` int(11) DEFAULT NULL COMMENT '入职时间',
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for lotus_user_log
-- ----------------------------
DROP TABLE IF EXISTS `lotus_user_log`;
CREATE TABLE `lotus_user_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT '',
  `url` varchar(255) DEFAULT '',
  `ip` varchar(150) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=489 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for lotus_goods
-- ----------------------------
DROP TABLE IF EXISTS `lotus_goods`;
CREATE TABLE `lotus_goods` (
  `id` int(12) NOT NULL  AUTO_INCREMENT,
  `goodsname` varchar(255) NOT NULL COMMENT '产品名称',
  `sku` varchar(100) NOT NULL COMMENT '产品货号',
  `barcode` varchar(32) NOT NULL COMMENT '条码',
  `unit` varchar(8) NOT NULL COMMENT '单位',
  `spec` VARCHAR(20) NULL COMMENT '规格',
  `lead_time` INT(11) NULL COMMENT '交期',
  `category` smallint(4) NOT NULL COMMENT '分类',
  `min_pack` varchar(12) DEFAULT NULL COMMENT '最小包装',
  `contact` smallint(4) DEFAULT NULL COMMENT '对应往来单位',
  `min_qty` int(4) NOT NULL DEFAULT '1' COMMENT '最小订量',
  `net` float(8,2) NOT NULL DEFAULT '0.00' COMMENT '净重',
  `wet` float(8,2) NOT NULL DEFAULT '0.00' COMMENT '毛重',
  `org` varchar(100) DEFAULT NULL COMMENT '产地',
  `cost` float(8,2) NOT NULL DEFAULT '0.00' COMMENT '成本',
  `price` float(8,2) NOT NULL DEFAULT '0.00' COMMENT '价格',
  `word` varchar(32) DEFAULT NULL COMMENT '助记词',
  `overflow` smallint(1) NOT NULL DEFAULT '1' COMMENT '允许负出库',
  `comment` varchar(255) DEFAULT NULL COMMENT '备注',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `status` smallint(1) NOT NULL DEFAULT '1' COMMENT '产品状态',
  `shop` INT(4) NOT NULL DEFAULT '0' COMMENT '所属店铺',
  `sales` FLOAT(8,2) NOT NULL DEFAULT '0' COMMENT '产品销量',
  `replenishment` TINYINT(2) NOT NULL DEFAULT '10' COMMENT '补货数量',
  PRIMARY KEY (`id`),
  KEY `sku` (`sku`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `lotus_category`;
CREATE TABLE `lotus_category` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) NOT NULL COMMENT '分类名称',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '分类状态',
  `pid` int(4) NOT NULL DEFAULT '0' COMMENT '父ID',
  `sort` tinyint(4) NOT NULL DEFAULT '0' COMMENT '排序',
  `icon` VARCHAR(80) NULL COMMENT '分类图标',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for table `lotus_storehouse`
-- ----------------------------
DROP TABLE IF EXISTS `lotus_storehouse`;
CREATE TABLE `lotus_storehouse` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `house_name` varchar(100) NOT NULL COMMENT '仓库名称',
  `status` SMALLINT(1) NOT NULL DEFAULT '1' COMMENT '仓库状态',
  `shop` INT(8) NOT NULL COMMENT '所属店铺',
  `pos` SMALLINT(1) NOT NULL DEFAULT '0' COMMENT '设为POS仓库',
  `comment` varchar(255) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for table `viooma_shop`
-- ----------------------------
DROP TABLE IF EXISTS `lotus_shop`;
CREATE TABLE `lotus_shop` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `shop_name` varchar(100) NOT NULL COMMENT '店铺名称',
  `shop_phone` varchar(30) DEFAULT NULL COMMENT '门店电话',
  `shop_address` varchar(120) DEFAULT NULL COMMENT '门店地址',
  `shop_director` varchar(8) DEFAULT NULL COMMENT '门店负责人',
  `status` smallint(1) NOT NULL DEFAULT '1' COMMENT '门店状态',
  `comment` text COMMENT '门店简介',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for table `lotus_supplier`
-- ----------------------------

DROP TABLE IF EXISTS `lotus_supplier`;
CREATE TABLE `lotus_supplier` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `supplier_name` varchar(100) NOT NULL COMMENT '供应商名称',
  `supplier_director` varchar(8) DEFAULT NULL COMMENT '负责人',
  `supplier_phone` varchar(20) DEFAULT NULL COMMENT '联系电话',
  `supplier_address` varchar(200) DEFAULT NULL COMMENT '供应商地址',
  `status` smallint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `comment` varchar(255) DEFAULT NULL COMMENT '供应商简介',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for table `lotus_stocks`
-- ----------------------------
DROP TABLE IF EXISTS `lotus_stocks`;
CREATE TABLE `lotus_stocks` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `goods_id` int(8) NOT NULL COMMENT '商品ID',
  `numbers` float(10,2) NOT NULL COMMENT '商品数量',
  `house_id` int(4) NOT NULL COMMENT '仓库ID',
  `contact` INT(4) NOT NULL COMMENT '供应商ID',
  PRIMARY KEY (`id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for table `viooma_member`
-- ----------------------------
DROP TABLE IF EXISTS `lotus_member`;
CREATE TABLE `lotus_member` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `member_code` varchar(12) NOT NULL COMMENT '客户代码',
  `member_name` varchar(200) NOT NULL COMMENT '客户名称',
  `member_card` VARCHAR(18) NOT NULL COMMENT '身份证号',
  `member_phone` VARCHAR(11) NOT NULL COMMENT '手机号' ,
  `member_sname` varchar(50) DEFAULT NULL COMMENT '客户简称',
  `member_address` varchar(255) DEFAULT NULL COMMENT '客户地址',
  `member_category` smallint(2) NOT NULL COMMENT '客户分类',
  `province` VARCHAR(32) NULL COMMENT '省份',
  `city` VARCHAR(20) NULL COMMENT '城市',
  `area` VARCHAR(20) NULL COMMENT '区域',
  `member_site` varchar(255) DEFAULT NULL COMMENT '客户网址',
  `member_regtime` int(11) NOT NULL COMMENT '注册时间',
  `member_status` smallint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `member_handover` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '是否交接',
  `member_shop` INT(4) NOT NULL DEFAULT '0' COMMENT '所属店铺',
  `comment` text COMMENT '客户介绍',
  PRIMARY KEY (`id`),
  KEY `member_code` (`member_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for table `lotus_mcategory`
-- ----------------------------
DROP TABLE IF EXISTS `lotus_mcategory`;
CREATE TABLE `lotus_mcategory` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `mcategory_name` varchar(100) NOT NULL COMMENT '分类名称',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '分类状态',
  `pid` int(4) NOT NULL DEFAULT '0' COMMENT '父ID',
  `sort` tinyint(4) NOT NULL DEFAULT '0' COMMENT '排序',
  `icon` VARCHAR(80) NULL COMMENT '分类图标',
  `discount` FLOAT(4,2) NOT NULL DEFAULT '10' COMMENT '享受折扣',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for table `lotus_order`
-- ----------------------------
DROP TABLE IF EXISTS `lotus_order`;
CREATE TABLE `lotus_order` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `or_id` varchar(20) NOT NULL COMMENT '订单编号',
  `or_type` varchar(30) NOT NULL COMMENT '订单类型',
  `or_contact` tinyint(4) NOT NULL DEFAULT '0' COMMENT '往来单位编号',
  `or_user` varchar(30) NOT NULL COMMENT '操作员',
  `or_house` TINYINT(2) NOT NULL DEFAULT '0' COMMENT '仓库',
  `or_house1` TINYINT(2) NOT NULL DEFAULT '0' COMMENT '备用仓库ID',
  `or_verify_user` varchar(30) DEFAULT NULL COMMENT '审核员',
  `or_verify_status` smallint(1) NOT NULL DEFAULT '0' COMMENT '审核状态',
  `or_verify_time` int(11) DEFAULT NULL COMMENT '审核日期',
  `or_delivery_id` varchar(30) NULL COMMENT '送货人',
  `or_create_time` int(11) NOT NULL COMMENT '订单日期',
  `or_status` SMALLINT(1) NOT NULL DEFAULT '0' COMMENT '订单状态',
  `or_finish` smallint(1) NOT NULL DEFAULT '0' COMMENT '订单结束标志',
  `or_unique` VARCHAR(32) NULL DEFAULT NULL COMMENT '唯一码',
  `or_money` FLOAT(8,2) NULL DEFAULT '0' COMMENT '订单金额',
  `or_paied` TINYINT NOT NULL DEFAULT '0' COMMENT '付款状态',
  `or_shop` TINYINT(2) NOT NULL DEFAULT '0' COMMENT '所属店铺',
  `or_comment` varchar(200) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `or_id` (`or_id`),
  KEY `or_type` (`or_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for table `viooma_item`
-- ----------------------------
DROP TABLE IF EXISTS `lotus_item`;
CREATE TABLE `lotus_item` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `or_id` varchar(20) NOT NULL COMMENT '订单编号',
  `gd_id` INT(4) NOT NULL COMMENT '产品ID',
  `it_number` float(8,2) NOT NULL COMMENT '数量',
  `it_price` float(8,2) NOT NULL COMMENT '单价或单价ID',
  `it_discount` float(4,2) NOT NULL DEFAULT '10.00' COMMENT '商品折扣',
  PRIMARY KEY (`id`),
  KEY `or_id` (`or_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `lotus_member_card`;
CREATE TABLE `lotus_member_card` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `member_id` int(4) NOT NULL COMMENT '会员ID',
  `card_no` varchar(32) NOT NULL COMMENT '会员卡号',
  `card_pwd` VARCHAR(6) NOT NULL COMMENT '会员卡密码',
  `card_money` float(10,2) NOT NULL COMMENT '充值金额',
  `card_balance` FLOAT(10,2) NOT NULL COMMENT '卡余额',
  `card_give` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '赠送金额',
  `status` smallint(1) NOT NULL DEFAULT '1' COMMENT '是否启用',
  `channel` tinyint(1) NOT NULL COMMENT '收款途径',
  `card_time` INT(11) NOT NULL COMMENT '开卡时间',
  `comment` varchar(255) NOT NULL COMMENT '开卡说明',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `lotus_financial_details`;
CREATE TABLE `lotus_financial_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `f_type` smallint(1) NOT NULL DEFAULT '1' COMMENT '1表借进，0表贷出',
  `f_money` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '交易金额',
  `f_reason` varchar(255) NOT NULL COMMENT '交易事由',
  `f_username` varchar(30) NOT NULL COMMENT '经手人',
  `f_time` int(11) NOT NULL COMMENT '交易时间',
  `f_channel` smallint(2) NOT NULL COMMENT '交易途径',
  `f_come` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '金额来源，1表POS，2表会员',
  `handover` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否交接',
  `handtime` INT(11) NOT NULL DEFAULT '0' COMMENT '交接时间',
  `f_comment` varchar(200) NOT NULL COMMENT '交易备注',
  PRIMARY KEY (`id`),
  KEY `handover` (`handover`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `lotus_card_details`;
CREATE TABLE `lotus_card_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `card_no` varchar(32) NOT NULL COMMENT '会员卡',
  `money` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `type` smallint(6) NOT NULL DEFAULT '0' COMMENT '0消耗，1充值',
  `or_id` VARCHAR(32) NULL COMMENT '目标订单',
  `time` INT(11) NOT NULL COMMENT '交易时间',
  PRIMARY KEY (`id`),
  KEY `card_no` (`card_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for table `viooma_print`
-- ----------------------------
DROP TABLE IF EXISTS `lotus_print`;
CREATE TABLE `lotus_print` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `print_name` varchar(100) NOT NULL COMMENT '打印机名称',
  `print_sn` varchar(32) NOT NULL COMMENT '打印机序列号',
  `print_key` varchar(32) NOT NULL COMMENT '打印机Key',
  `print_brand` tinyint(1) NOT NULL COMMENT '打印机品牌',
  `print_shop` SMALLINT(4) NOT NULL COMMENT '店铺ID',
  `print_status` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '打印状态',
  `print_mould` TEXT NOT NULL COMMENT '打印模板',
  PRIMARY KEY (`id`),
  KEY `print_sn` (`print_sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `lotus_handover`;
CREATE TABLE `lotus_handover` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `hand_user` varchar(20) NOT NULL COMMENT '交接用户',
  `hand_cash` float(8,2) NOT NULL COMMENT '交接现金',
  `hand_wechat` float(8,2) NOT NULL COMMENT '交接微信',
  `hand_alipay` float(8,2) NOT NULL COMMENT '交接支付宝',
  `hand_keep` float(8,2) NOT NULL COMMENT '钱箱留存',
  `hand_shop` INT(4) NOT NULL COMMENT '交接店铺',
  `hand_time` int(11) NOT NULL COMMENT '交接时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;