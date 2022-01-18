/*
Navicat MySQL Data Transfer

Source Server         : 本地
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : dev

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2019-07-22 17:00:57
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for lotus_addon
-- ----------------------------
DROP TABLE IF EXISTS `lotus_addon`;
CREATE TABLE `lotus_addon` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '插件名称',
  `version` float(30,1) NOT NULL DEFAULT '1.0',
  `hash` varchar(255) NOT NULL DEFAULT '' COMMENT '哈希值',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态 1未安装  2已安装 3启用',
  `des` varchar(255) NOT NULL DEFAULT '' COMMENT '介绍',
  `author` varchar(50) NOT NULL DEFAULT '',
  `price` decimal(10,1) NOT NULL DEFAULT '0.0' COMMENT '价格',
  `download_times` int(10) NOT NULL DEFAULT '0' COMMENT '下载次数',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of lotus_addon
-- ----------------------------
INSERT INTO `lotus_addon` VALUES ('1', '登陆美化', '1.0', '', '0', '登陆美化', '阿修罗', '0.0', '0');

-- ----------------------------
-- Table structure for lotus_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `lotus_auth_group`;
CREATE TABLE `lotus_auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(100) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `rules` varchar(255) NOT NULL COMMENT '权限规则ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=225 DEFAULT CHARSET=utf8 COMMENT='权限组表';

-- ----------------------------
-- Records of lotus_auth_group
-- ----------------------------
INSERT INTO `lotus_auth_group` VALUES ('1', '超级管理组', '1', '1,2,223,224,225,3,220,221,222,4,219,226,228,227,229,236,237');
INSERT INTO `lotus_auth_group` VALUES ('193', '普通用户', '1', '1,2,223,224,225,3,220,221,222,4,219,226,228,227,229,236,237');
INSERT INTO `lotus_auth_group` VALUES ('224', '测试权限', '1', '1,2,223,224,225,3,220,221,222,4,219,226,228,238,229,236,237');

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
INSERT INTO `lotus_auth_group_access` VALUES ('146', '193');
INSERT INTO `lotus_auth_group_access` VALUES ('147', '224');
INSERT INTO `lotus_auth_group_access` VALUES ('148', '224');
INSERT INTO `lotus_auth_group_access` VALUES ('149', '224');
INSERT INTO `lotus_auth_group_access` VALUES ('150', '224');
INSERT INTO `lotus_auth_group_access` VALUES ('151', '224');
INSERT INTO `lotus_auth_group_access` VALUES ('152', '224');
INSERT INTO `lotus_auth_group_access` VALUES ('153', '224');
INSERT INTO `lotus_auth_group_access` VALUES ('154', '224');
INSERT INTO `lotus_auth_group_access` VALUES ('155', '224');
INSERT INTO `lotus_auth_group_access` VALUES ('156', '224');
INSERT INTO `lotus_auth_group_access` VALUES ('157', '224');

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
) ENGINE=MyISAM AUTO_INCREMENT=239 DEFAULT CHARSET=utf8 COMMENT='规则表';

-- ----------------------------
-- Records of lotus_auth_rule
-- ----------------------------
INSERT INTO `lotus_auth_rule` VALUES ('1', 'admin/user/default', '后台管理', '1', '1', '0', 'layui-icon-username', '0', '');
INSERT INTO `lotus_auth_rule` VALUES ('2', 'admin/user/userList', '用户管理', '1', '1', '1', '', '1', '');
INSERT INTO `lotus_auth_rule` VALUES ('3', 'admin/user/ruleList', '权限管理', '1', '1', '1', '', '1', '');
INSERT INTO `lotus_auth_rule` VALUES ('4', 'admin/user/roleList', '角色管理', '1', '1', '1', '', '0', '');
INSERT INTO `lotus_auth_rule` VALUES ('219', 'admin/user/addRole', '新增角色', '1', '0', '4', '', '0', '');
INSERT INTO `lotus_auth_rule` VALUES ('220', 'admin/user/addRule', '新增权限', '1', '0', '3', '', '0', '');
INSERT INTO `lotus_auth_rule` VALUES ('221', 'admin/user/editRule', '编辑权限', '1', '0', '3', '', '0', '');
INSERT INTO `lotus_auth_rule` VALUES ('222', 'admin/user/deleteRule', '删除权限', '1', '0', '3', '', '0', '');
INSERT INTO `lotus_auth_rule` VALUES ('223', 'admin/User/addUser', '增加用户', '1', '0', '2', '', '0', '');
INSERT INTO `lotus_auth_rule` VALUES ('224', 'admin/user/editUser', '编辑用户', '1', '0', '2', '', '0', '');
INSERT INTO `lotus_auth_rule` VALUES ('225', 'admin/user/deleteUser', '删除用户', '1', '0', '2', '', '0', '');
INSERT INTO `lotus_auth_rule` VALUES ('226', 'admin/user/editRole', '角色授权', '1', '0', '4', '', '0', '');
INSERT INTO `lotus_auth_rule` VALUES ('238', 'admin/user/editpasswd', '修改密码', '1', '0', '0', '', '0', '');
INSERT INTO `lotus_auth_rule` VALUES ('228', 'admin/user/delRole', '删除角色', '1', '0', '4', '', '0', '');
INSERT INTO `lotus_auth_rule` VALUES ('229', 'admin/userLog/index', '系统日志', '1', '1', '0', 'layui-icon-log', '1', '');

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
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=158 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
-- Records of lotus_user
-- ----------------------------

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
) ENGINE=InnoDB AUTO_INCREMENT=289 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;


INSERT INTO `lotus_user_log` (`id`, `uid`, `name`, `url`, `ip`, `create_time`) VALUES ('3', '1', '登陆系统', 'admin/login/login', '127.0.0.1', '1562938948');
INSERT INTO `lotus_user_log` (`id`, `uid`, `name`, `url`, `ip`, `create_time`) VALUES ('4', '1', '修改密码', 'admin/user/editPasswd', '127.0.0.1', '1562939046');
INSERT INTO `lotus_user_log` (`id`, `uid`, `name`, `url`, `ip`, `create_time`) VALUES ('5', '1', '登陆系统', 'admin/login/login', '127.0.0.1', '1562939053');
INSERT INTO `lotus_user_log` (`id`, `uid`, `name`, `url`, `ip`, `create_time`) VALUES ('6', '1', '登陆系统', 'admin/login/login', '127.0.0.1', '1562939333');
INSERT INTO `lotus_user_log` (`id`, `uid`, `name`, `url`, `ip`, `create_time`) VALUES ('7', '1', '修改密码', 'admin/user/editPasswd', '127.0.0.1', '1562939354');
INSERT INTO `lotus_user_log` (`id`, `uid`, `name`, `url`, `ip`, `create_time`) VALUES ('8', '1', '登陆系统', 'admin/login/login', '127.0.0.1', '1562939361');
INSERT INTO `lotus_user_log` (`id`, `uid`, `name`, `url`, `ip`, `create_time`) VALUES ('9', '0', '登陆界面', 'admin/login/login', '127.0.0.1', '1562939482');
INSERT INTO `lotus_user_log` (`id`, `uid`, `name`, `url`, `ip`, `create_time`) VALUES ('10', '1', '登陆系统', 'admin/login/login', '127.0.0.1', '1562939500');
INSERT INTO `lotus_user_log` (`id`, `uid`, `name`, `url`, `ip`, `create_time`) VALUES ('11', '0', '访问登陆界面', 'admin/login/login', '127.0.0.1', '1562939571');
INSERT INTO `lotus_user_log` (`id`, `uid`, `name`, `url`, `ip`, `create_time`) VALUES ('12', '0', '访问登陆界面', 'admin/login/login', '127.0.0.1', '1562939573');
INSERT INTO `lotus_user_log` (`id`, `uid`, `name`, `url`, `ip`, `create_time`) VALUES ('13', '1', '登陆系统', 'admin/login/login', '127.0.0.1', '1562939590');
INSERT INTO `lotus_user_log` (`id`, `uid`, `name`, `url`, `ip`, `create_time`) VALUES ('14', '0', '访问登陆界面', 'admin/login/login', '127.0.0.1', '1562939777');
INSERT INTO `lotus_user_log` (`id`, `uid`, `name`, `url`, `ip`, `create_time`) VALUES ('15', '0', '访问登陆界面', 'admin/login/login', '127.0.0.1', '1562939781');
INSERT INTO `lotus_user_log` (`id`, `uid`, `name`, `url`, `ip`, `create_time`) VALUES ('16', '1', '登陆系统', 'admin/login/login', '127.0.0.1', '1562939788');
INSERT INTO `lotus_user_log` (`id`, `uid`, `name`, `url`, `ip`, `create_time`) VALUES ('17', '1', '添加用户ceasd', 'admin/user/addUser', '127.0.0.1', '1562940052');
INSERT INTO `lotus_user_log` (`id`, `uid`, `name`, `url`, `ip`, `create_time`) VALUES ('18', '1', '添加角色aszxc', 'admin/user/addRole', '127.0.0.1', '1562940183');
INSERT INTO `lotus_user_log` (`id`, `uid`, `name`, `url`, `ip`, `create_time`) VALUES ('19', '1', '注销系统', 'admin/login/logout', '127.0.0.1', '1562940386');
INSERT INTO `lotus_user_log` (`id`, `uid`, `name`, `url`, `ip`, `create_time`) VALUES ('20', '0', '访问登陆界面', 'admin/login/login', '127.0.0.1', '1562940388');
INSERT INTO `lotus_user_log` (`id`, `uid`, `name`, `url`, `ip`, `create_time`) VALUES ('21', '1', '登陆系统', 'admin/login/login', '127.0.0.1', '1562940400');
INSERT INTO `lotus_user_log` (`id`, `uid`, `name`, `url`, `ip`, `create_time`) VALUES ('22', '1', '删除角色asdasdasd', 'admin/user/delRole', '127.0.0.1', '1562941255');
INSERT INTO `lotus_user_log` (`id`, `uid`, `name`, `url`, `ip`, `create_time`) VALUES ('23', '1', '删除用户ceasd', 'admin/user/deleteUser', '127.0.0.1', '1562941453');
INSERT INTO `lotus_user_log` (`id`, `uid`, `name`, `url`, `ip`, `create_time`) VALUES ('24', '1', '删除用户ceshi', 'admin/user/deleteUser', '127.0.0.1', '1562941472');

