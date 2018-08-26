SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `auth_key` varchar(32) NOT NULL,
  `password_hash` varchar(256) NOT NULL,
  `password_reset_token` varchar(256) DEFAULT NULL,
  `email` varchar(256) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '10',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin
-- ----------------------------
INSERT INTO `admin` VALUES ('1', 'admin', 'Rum3iqM2JAsk-VVoPE2XvNpMiVZhZ8DB', '$2y$13$Mn7/d1kXiHEhf5gf9G3l3ui9vbOHiZ5dFb.ONk2rXZFHEYN2OCzRK', null, 'admin@qq.com', '10', '1534756799', '1534817103');

-- ----------------------------
-- Table structure for auth_assignment
-- ----------------------------
DROP TABLE IF EXISTS `auth_assignment`;
CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) NOT NULL,
  `user_id` varchar(64) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  KEY `auth_assignment_user_id_idx` (`user_id`),
  CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of auth_assignment
-- ----------------------------
INSERT INTO `auth_assignment` VALUES ('超级管理员', '1', '1534762780');

-- ----------------------------
-- Table structure for auth_item
-- ----------------------------
DROP TABLE IF EXISTS `auth_item`;
CREATE TABLE `auth_item` (
  `name` varchar(64) NOT NULL,
  `type` smallint(6) NOT NULL,
  `description` text,
  `rule_name` varchar(64) DEFAULT NULL,
  `data` blob,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `type` (`type`),
  CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of auth_item
-- ----------------------------
INSERT INTO `auth_item` VALUES ('/*', '2', null, null, null, '1534762001', '1534762001');
INSERT INTO `auth_item` VALUES ('/admin/*', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/assignment/*', '2', null, null, null, '1534759857', '1534759857');
INSERT INTO `auth_item` VALUES ('/admin/assignment/assign', '2', null, null, null, '1534759857', '1534759857');
INSERT INTO `auth_item` VALUES ('/admin/assignment/index', '2', null, null, null, '1534759857', '1534759857');
INSERT INTO `auth_item` VALUES ('/admin/assignment/revoke', '2', null, null, null, '1534759857', '1534759857');
INSERT INTO `auth_item` VALUES ('/admin/assignment/view', '2', null, null, null, '1534759857', '1534759857');
INSERT INTO `auth_item` VALUES ('/admin/default/*', '2', null, null, null, '1534759857', '1534759857');
INSERT INTO `auth_item` VALUES ('/admin/default/index', '2', null, null, null, '1534759857', '1534759857');
INSERT INTO `auth_item` VALUES ('/admin/menu/*', '2', null, null, null, '1534759857', '1534759857');
INSERT INTO `auth_item` VALUES ('/admin/menu/create', '2', null, null, null, '1534759857', '1534759857');
INSERT INTO `auth_item` VALUES ('/admin/menu/delete', '2', null, null, null, '1534759857', '1534759857');
INSERT INTO `auth_item` VALUES ('/admin/menu/index', '2', null, null, null, '1534759857', '1534759857');
INSERT INTO `auth_item` VALUES ('/admin/menu/update', '2', null, null, null, '1534759857', '1534759857');
INSERT INTO `auth_item` VALUES ('/admin/menu/view', '2', null, null, null, '1534759857', '1534759857');
INSERT INTO `auth_item` VALUES ('/admin/permission/*', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/permission/assign', '2', null, null, null, '1534759857', '1534759857');
INSERT INTO `auth_item` VALUES ('/admin/permission/create', '2', null, null, null, '1534759857', '1534759857');
INSERT INTO `auth_item` VALUES ('/admin/permission/delete', '2', null, null, null, '1534759857', '1534759857');
INSERT INTO `auth_item` VALUES ('/admin/permission/index', '2', null, null, null, '1534759857', '1534759857');
INSERT INTO `auth_item` VALUES ('/admin/permission/remove', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/permission/update', '2', null, null, null, '1534759857', '1534759857');
INSERT INTO `auth_item` VALUES ('/admin/permission/view', '2', null, null, null, '1534759857', '1534759857');
INSERT INTO `auth_item` VALUES ('/admin/role/*', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/role/assign', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/role/create', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/role/delete', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/role/index', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/role/remove', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/role/update', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/role/view', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/route/*', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/route/assign', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/route/create', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/route/index', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/route/refresh', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/route/remove', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/rule/*', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/rule/create', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/rule/delete', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/rule/index', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/rule/update', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/rule/view', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/user/*', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/user/activate', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/user/change-password', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/user/delete', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/user/index', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/user/login', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/user/logout', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/user/request-password-reset', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/user/reset-password', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/user/signup', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/admin/user/view', '2', null, null, null, '1534759858', '1534759858');
INSERT INTO `auth_item` VALUES ('/base/*', '2', null, null, null, '1534767715', '1534767715');
INSERT INTO `auth_item` VALUES ('/cart/*', '2', null, null, null, '1535094825', '1535094825');
INSERT INTO `auth_item` VALUES ('/cart/add-list', '2', null, null, null, '1535094825', '1535094825');
INSERT INTO `auth_item` VALUES ('/cart/delete-cart', '2', null, null, null, '1535094825', '1535094825');
INSERT INTO `auth_item` VALUES ('/cart/list', '2', null, null, null, '1535094825', '1535094825');
INSERT INTO `auth_item` VALUES ('/debug/*', '2', null, null, null, '1534762793', '1534762793');
INSERT INTO `auth_item` VALUES ('/debug/default/*', '2', null, null, null, '1534762792', '1534762792');
INSERT INTO `auth_item` VALUES ('/debug/default/db-explain', '2', null, null, null, '1534762792', '1534762792');
INSERT INTO `auth_item` VALUES ('/debug/default/download-mail', '2', null, null, null, '1534762792', '1534762792');
INSERT INTO `auth_item` VALUES ('/debug/default/index', '2', null, null, null, '1534762792', '1534762792');
INSERT INTO `auth_item` VALUES ('/debug/default/toolbar', '2', null, null, null, '1534762792', '1534762792');
INSERT INTO `auth_item` VALUES ('/debug/default/view', '2', null, null, null, '1534762792', '1534762792');
INSERT INTO `auth_item` VALUES ('/debug/user/*', '2', null, null, null, '1534762792', '1534762792');
INSERT INTO `auth_item` VALUES ('/debug/user/reset-identity', '2', null, null, null, '1534762792', '1534762792');
INSERT INTO `auth_item` VALUES ('/debug/user/set-identity', '2', null, null, null, '1534762792', '1534762792');
INSERT INTO `auth_item` VALUES ('/gii/*', '2', null, null, null, '1534762793', '1534762793');
INSERT INTO `auth_item` VALUES ('/gii/default/*', '2', null, null, null, '1534762793', '1534762793');
INSERT INTO `auth_item` VALUES ('/gii/default/action', '2', null, null, null, '1534762793', '1534762793');
INSERT INTO `auth_item` VALUES ('/gii/default/diff', '2', null, null, null, '1534762793', '1534762793');
INSERT INTO `auth_item` VALUES ('/gii/default/index', '2', null, null, null, '1534762793', '1534762793');
INSERT INTO `auth_item` VALUES ('/gii/default/preview', '2', null, null, null, '1534762793', '1534762793');
INSERT INTO `auth_item` VALUES ('/gii/default/view', '2', null, null, null, '1534762793', '1534762793');
INSERT INTO `auth_item` VALUES ('/inquiry-better/*', '2', null, null, null, '1535008017', '1535008017');
INSERT INTO `auth_item` VALUES ('/inquiry-better/create', '2', null, null, null, '1535008017', '1535008017');
INSERT INTO `auth_item` VALUES ('/inquiry-better/delete', '2', null, null, null, '1535008017', '1535008017');
INSERT INTO `auth_item` VALUES ('/inquiry-better/index', '2', null, null, null, '1535008017', '1535008017');
INSERT INTO `auth_item` VALUES ('/inquiry-better/sort', '2', null, null, null, '1535008017', '1535008017');
INSERT INTO `auth_item` VALUES ('/inquiry-better/status', '2', null, null, null, '1535008017', '1535008017');
INSERT INTO `auth_item` VALUES ('/inquiry-better/update', '2', null, null, null, '1535008017', '1535008017');
INSERT INTO `auth_item` VALUES ('/inquiry-better/view', '2', null, null, null, '1535008017', '1535008017');
INSERT INTO `auth_item` VALUES ('/inquiry/*', '2', null, null, null, '1534770158', '1534770158');
INSERT INTO `auth_item` VALUES ('/inquiry/create', '2', null, null, null, '1534770158', '1534770158');
INSERT INTO `auth_item` VALUES ('/inquiry/delete', '2', null, null, null, '1534770158', '1534770158');
INSERT INTO `auth_item` VALUES ('/inquiry/index', '2', null, null, null, '1534770158', '1534770158');
INSERT INTO `auth_item` VALUES ('/inquiry/sort', '2', null, null, null, '1534990829', '1534990829');
INSERT INTO `auth_item` VALUES ('/inquiry/status', '2', null, null, null, '1534990829', '1534990829');
INSERT INTO `auth_item` VALUES ('/inquiry/update', '2', null, null, null, '1534770158', '1534770158');
INSERT INTO `auth_item` VALUES ('/inquiry/view', '2', null, null, null, '1534770158', '1534770158');
INSERT INTO `auth_item` VALUES ('/order-inquiry/*', '2', null, null, null, '1535094826', '1535094826');
INSERT INTO `auth_item` VALUES ('/order-inquiry/delete', '2', null, null, null, '1535094826', '1535094826');
INSERT INTO `auth_item` VALUES ('/order-inquiry/index', '2', null, null, null, '1535094825', '1535094825');
INSERT INTO `auth_item` VALUES ('/order-inquiry/sort', '2', null, null, null, '1535094825', '1535094825');
INSERT INTO `auth_item` VALUES ('/order-inquiry/submit', '2', null, null, null, '1535094825', '1535094825');
INSERT INTO `auth_item` VALUES ('/order-quote/*', '2', null, null, null, '1535095525', '1535095525');
INSERT INTO `auth_item` VALUES ('/order-quote/create', '2', null, null, null, '1535095525', '1535095525');
INSERT INTO `auth_item` VALUES ('/order-quote/delete', '2', null, null, null, '1535095525', '1535095525');
INSERT INTO `auth_item` VALUES ('/order-quote/index', '2', null, null, null, '1535095525', '1535095525');
INSERT INTO `auth_item` VALUES ('/order-quote/update', '2', null, null, null, '1535095525', '1535095525');
INSERT INTO `auth_item` VALUES ('/order-quote/view', '2', null, null, null, '1535095525', '1535095525');
INSERT INTO `auth_item` VALUES ('/search/*', '2', null, null, null, '1534990829', '1534990829');
INSERT INTO `auth_item` VALUES ('/search/get', '2', null, null, null, '1535094826', '1535094826');
INSERT INTO `auth_item` VALUES ('/search/get-good-id', '2', null, null, null, '1534990829', '1534990829');
INSERT INTO `auth_item` VALUES ('/search/index', '2', null, null, null, '1534990829', '1534990829');
INSERT INTO `auth_item` VALUES ('/search/search', '2', null, null, null, '1534990829', '1534990829');
INSERT INTO `auth_item` VALUES ('/site/*', '2', null, null, null, '1534762793', '1534762793');
INSERT INTO `auth_item` VALUES ('/site/about', '2', null, null, null, '1535248012', '1535248012');
INSERT INTO `auth_item` VALUES ('/site/captcha', '2', null, null, null, '1535248011', '1535248011');
INSERT INTO `auth_item` VALUES ('/site/contact', '2', null, null, null, '1535248011', '1535248011');
INSERT INTO `auth_item` VALUES ('/site/error', '2', null, null, null, '1534762793', '1534762793');
INSERT INTO `auth_item` VALUES ('/site/index', '2', null, null, null, '1534762793', '1534762793');
INSERT INTO `auth_item` VALUES ('/site/login', '2', null, null, null, '1534762793', '1534762793');
INSERT INTO `auth_item` VALUES ('/site/logout', '2', null, null, null, '1534762793', '1534762793');
INSERT INTO `auth_item` VALUES ('/stock/*', '2', null, null, null, '1534767716', '1534767716');
INSERT INTO `auth_item` VALUES ('/stock/create', '2', null, null, null, '1534767715', '1534767715');
INSERT INTO `auth_item` VALUES ('/stock/delete', '2', null, null, null, '1534767715', '1534767715');
INSERT INTO `auth_item` VALUES ('/stock/index', '2', null, null, null, '1534767715', '1534767715');
INSERT INTO `auth_item` VALUES ('/stock/sort', '2', null, null, null, '1534767715', '1534767715');
INSERT INTO `auth_item` VALUES ('/stock/status', '2', null, null, null, '1534767716', '1534767716');
INSERT INTO `auth_item` VALUES ('/stock/update', '2', null, null, null, '1534767715', '1534767715');
INSERT INTO `auth_item` VALUES ('/stock/view', '2', null, null, null, '1534767716', '1534767716');
INSERT INTO `auth_item` VALUES ('/supplier/*', '2', null, null, null, '1534762793', '1534762793');
INSERT INTO `auth_item` VALUES ('/supplier/create', '2', null, null, null, '1534762793', '1534762793');
INSERT INTO `auth_item` VALUES ('/supplier/delete', '2', null, null, null, '1534762793', '1534762793');
INSERT INTO `auth_item` VALUES ('/supplier/index', '2', null, null, null, '1534762793', '1534762793');
INSERT INTO `auth_item` VALUES ('/supplier/sort', '2', null, null, null, '1534767716', '1534767716');
INSERT INTO `auth_item` VALUES ('/supplier/status', '2', null, null, null, '1534767716', '1534767716');
INSERT INTO `auth_item` VALUES ('/supplier/update', '2', null, null, null, '1534762793', '1534762793');
INSERT INTO `auth_item` VALUES ('/supplier/view', '2', null, null, null, '1534762793', '1534762793');
INSERT INTO `auth_item` VALUES ('超级管理员', '1', null, null, null, '1534756870', '1534762040');

-- ----------------------------
-- Table structure for auth_item_child
-- ----------------------------
DROP TABLE IF EXISTS `auth_item_child`;
CREATE TABLE `auth_item_child` (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`),
  CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of auth_item_child
-- ----------------------------
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/*');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/*');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/assignment/*');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/assignment/assign');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/assignment/index');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/assignment/revoke');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/assignment/view');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/default/*');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/default/index');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/menu/*');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/menu/create');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/menu/delete');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/menu/index');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/menu/update');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/menu/view');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/permission/*');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/permission/assign');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/permission/create');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/permission/delete');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/permission/index');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/permission/remove');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/permission/update');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/permission/view');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/role/*');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/role/assign');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/role/create');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/role/delete');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/role/index');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/role/remove');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/role/update');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/role/view');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/route/*');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/route/assign');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/route/create');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/route/index');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/route/refresh');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/route/remove');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/rule/*');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/rule/create');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/rule/delete');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/rule/index');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/rule/update');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/rule/view');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/user/*');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/user/activate');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/user/change-password');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/user/delete');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/user/index');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/user/login');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/user/logout');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/user/request-password-reset');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/user/reset-password');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/user/signup');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '/admin/user/view');

-- ----------------------------
-- Table structure for auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `auth_rule`;
CREATE TABLE `auth_rule` (
  `name` varchar(64) NOT NULL,
  `data` blob,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of auth_rule
-- ----------------------------

-- ----------------------------
-- Table structure for cart
-- ----------------------------
DROP TABLE IF EXISTS `cart`;
CREATE TABLE `cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `inquiry_id` int(11) NOT NULL DEFAULT '0' COMMENT '询价id',
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '类型：0最新 1优选 2库存',
  `number` int(11) NOT NULL DEFAULT '0' COMMENT '购买数量',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='购物车';

-- ----------------------------
-- Records of cart
-- ----------------------------
INSERT INTO `cart` VALUES ('17', '6', '0', '1', '2018-08-26 21:58:17', '2018-08-26 21:58:11');

-- ----------------------------
-- Table structure for inquiry
-- ----------------------------
DROP TABLE IF EXISTS `inquiry`;
CREATE TABLE `inquiry` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `good_id` varchar(255) NOT NULL DEFAULT '' COMMENT '零件编号',
  `supplier_id` int(11) NOT NULL DEFAULT '0' COMMENT '供应商ID',
  `supplier_name` varchar(255) NOT NULL DEFAULT '0' COMMENT '供应商名称',
  `inquiry_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '咨询价格',
  `inquiry_datetime` varchar(255) NOT NULL DEFAULT '' COMMENT '咨询时间',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `is_better` int(11) NOT NULL DEFAULT '0' COMMENT '是否优选：0否 1是',
  `is_newest` int(11) NOT NULL DEFAULT '0' COMMENT '是否最新询价：0否 1是',
  `is_priority` int(11) NOT NULL DEFAULT '0' COMMENT '是否优先询价： 0否 1是',
  `is_deleted` int(11) NOT NULL DEFAULT '0' COMMENT '是否删除：0未删除 1已删除',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='询价表';

-- ----------------------------
-- Records of inquiry
-- ----------------------------
INSERT INTO `inquiry` VALUES ('1', '010', '1', '中国移动', '2.50', '2018-08-21 14:55:00', '0', '1', '0', '1', '0', '2018-08-23 15:21:20', '2018-08-21 13:53:23');
INSERT INTO `inquiry` VALUES ('2', '010', '1', '中国移动', '15.00', '2018-08-21 14:05:00', '0', '1', '0', '0', '0', '2018-08-22 13:57:02', '2018-08-21 14:21:23');
INSERT INTO `inquiry` VALUES ('3', '010', '1', '中国移动', '13.00', '2018-08-21 14:20:00', '0', '1', '0', '0', '0', '2018-08-22 13:57:02', '2018-08-21 14:21:58');
INSERT INTO `inquiry` VALUES ('4', '011', '1', '中国移动', '1.00', '2018-08-23 10:45:00', '0', '1', '0', '0', '0', '2018-08-23 09:58:15', '2018-08-23 09:56:49');
INSERT INTO `inquiry` VALUES ('5', '001', '1', '中国移动', '15.00', '2018-08-23 09:55:00', '0', '1', '1', '0', '0', '2018-08-23 09:58:15', '2018-08-23 09:58:15');
INSERT INTO `inquiry` VALUES ('6', '012', '1', '中国移动', '12.00', '2018-08-23 10:00:00', '0', '1', '1', '0', '0', '2018-08-23 10:00:24', '2018-08-23 10:00:24');
INSERT INTO `inquiry` VALUES ('7', '011', '1', '中国移动', '11.00', '2018-08-23 10:05:00', '0', '1', '0', '0', '0', '2018-08-23 10:00:56', '2018-08-23 10:00:56');
INSERT INTO `inquiry` VALUES ('8', '011', '1', '中国移动', '15.00', '2018-08-23 11:00:00', '0', '1', '1', '0', '0', '2018-08-23 10:03:17', '2018-08-23 10:03:17');
INSERT INTO `inquiry` VALUES ('9', '010', '1', '中国移动', '12.00', '2018-08-23 10:05:00', '0', '1', '1', '0', '0', '2018-08-23 10:05:23', '2018-08-23 10:05:23');
INSERT INTO `inquiry` VALUES ('10', '002', '1', '中国移动', '12.00', '2018-08-24 10:10:00', '0', '1', '1', '0', '0', '2018-08-24 10:12:00', '2018-08-24 10:12:00');
INSERT INTO `inquiry` VALUES ('11', '003', '1', '中国移动', '14.00', '2018-08-24 10:10:00', '0', '1', '1', '0', '0', '2018-08-24 10:12:50', '2018-08-24 10:12:50');

-- ----------------------------
-- Table structure for menu
-- ----------------------------
DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT NULL,
  `parent` int(11) DEFAULT NULL,
  `route` varchar(256) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `data` blob,
  PRIMARY KEY (`id`),
  KEY `parent` (`parent`),
  CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `menu` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of menu
-- ----------------------------
INSERT INTO `menu` VALUES ('1', '供应商管理', null, '/supplier/index', '4', 0x7573657273);
INSERT INTO `menu` VALUES ('2', '库存管理', null, '/stock/index', '7', 0x686F6D65);
INSERT INTO `menu` VALUES ('3', '询价管理', null, '/inquiry/index', '5', 0x7461736B73);
INSERT INTO `menu` VALUES ('4', '新建报价单/询价单', null, '/search/index', '1', 0x736561726368);
INSERT INTO `menu` VALUES ('5', '优先询价管理', null, '/inquiry-better/index', '6', 0x7468756D62732D7570);
INSERT INTO `menu` VALUES ('6', '询价单查询', null, '/order-inquiry/index', '3', 0x6E617669636F6E);
INSERT INTO `menu` VALUES ('7', '报价单查询', null, '/order-quote/index', '2', 0x6C6973742D756C);

-- ----------------------------
-- Table structure for order_inquiry
-- ----------------------------
DROP TABLE IF EXISTS `order_inquiry`;
CREATE TABLE `order_inquiry` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `order_id` varchar(255) NOT NULL DEFAULT '' COMMENT '订单编号',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `quote_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '咨询价格',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `inquirys` text NOT NULL COMMENT '询价id列表 json',
  `stocks` varchar(255) NOT NULL DEFAULT '' COMMENT '库存id列表 json',
  `is_deleted` int(11) NOT NULL DEFAULT '0' COMMENT '是否删除：0未删除 1已删除',
  `provide_date` datetime DEFAULT NULL COMMENT '供货日期',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='询价单';

-- ----------------------------
-- Records of order_inquiry
-- ----------------------------
INSERT INTO `order_inquiry` VALUES ('2', '987654', '第一次订单', '1500.00', '这次是对的 ', '[{\"type\":\"0\",\"list\":[{\"id\":5,\"number\":1},{\"id\":6,\"number\":2},{\"id\":8,\"number\":3},{\"id\":9,\"number\":4}]},{\"type\":\"1\",\"list\":[{\"id\":1,\"number\":5},{\"id\":2,\"number\":6},{\"id\":3,\"number\":7},{\"id\":4,\"number\":8}]},{\"type\":\"2\",\"list\":[{\"id\":1,\"number\":9},{\"id\":2,\"number\":2},{\"id\":3,\"number\":10}]}]', '', '0', '2018-08-24 17:39:00', '2018-08-24 17:39:47', '2018-08-24 17:39:47');

-- ----------------------------
-- Table structure for order_quote
-- ----------------------------
DROP TABLE IF EXISTS `order_quote`;
CREATE TABLE `order_quote` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `order_id` varchar(255) NOT NULL DEFAULT '' COMMENT '订单编号',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `quote_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '咨询价格',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `inquirys` text NOT NULL COMMENT '询价id列表 json',
  `stocks` varchar(255) NOT NULL DEFAULT '' COMMENT '库存id列表 json',
  `is_deleted` int(11) NOT NULL DEFAULT '0' COMMENT '是否删除：0未删除 1已删除',
  `provide_date` datetime DEFAULT NULL COMMENT '供货日期',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='报价单';

-- ----------------------------
-- Records of order_quote
-- ----------------------------
INSERT INTO `order_quote` VALUES ('4', '987654', '第一次订单', '1400.00', '对外销售', '[{\"type\":\"0\",\"list\":[{\"id\":5,\"number\":1},{\"id\":6,\"number\":2},{\"id\":8,\"number\":3},{\"id\":9,\"number\":4}]},{\"type\":\"1\",\"list\":[{\"id\":1,\"number\":5},{\"id\":2,\"number\":6},{\"id\":3,\"number\":7},{\"id\":4,\"number\":8}]},{\"type\":\"2\",\"list\":[{\"id\":1,\"number\":9},{\"id\":2,\"number\":2},{\"id\":3,\"number\":10}]}]', '', '0', '2018-08-24 17:24:00', '2018-08-24 17:26:30', '2018-08-24 17:26:30');
INSERT INTO `order_quote` VALUES ('5', '1', '1', '120.00', '1', '[{\"type\":\"0\",\"list\":[{\"id\":5,\"number\":1}]},{\"type\":\"1\",\"list\":[{\"id\":1,\"number\":1}]},{\"type\":\"2\",\"list\":[{\"id\":1,\"number\":1}]}]', '', '0', '2018-08-26 21:24:00', '2018-08-26 21:33:02', '2018-08-26 21:33:04');
INSERT INTO `order_quote` VALUES ('6', '010231', '撒的发', '800.00', '哥们', '[{\"type\":\"0\",\"list\":[{\"id\":5,\"number\":10},{\"id\":6,\"number\":9},{\"id\":8,\"number\":8},{\"id\":9,\"number\":7}]},{\"type\":\"1\",\"list\":[{\"id\":1,\"number\":5},{\"id\":2,\"number\":4},{\"id\":3,\"number\":3},{\"id\":4,\"number\":2}]},{\"type\":\"2\",\"list\":[{\"id\":1,\"number\":1},{\"id\":2,\"number\":2},{\"id\":3,\"number\":3},{\"id\":4,\"number\":4}]}]', '', '0', '2018-08-26 21:28:00', '2018-08-26 21:29:31', '2018-08-26 21:29:31');

-- ----------------------------
-- Table structure for stock
-- ----------------------------
DROP TABLE IF EXISTS `stock`;
CREATE TABLE `stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `good_id` varchar(255) NOT NULL DEFAULT '' COMMENT '零件编号',
  `supplier_id` int(11) NOT NULL DEFAULT '0' COMMENT '供应商ID',
  `supplier_name` varchar(255) NOT NULL DEFAULT '0' COMMENT '供应商名称',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '价格',
  `position` varchar(255) NOT NULL DEFAULT '' COMMENT '库存位置',
  `number` int(11) NOT NULL DEFAULT '0' COMMENT '库存数量',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `is_deleted` int(11) NOT NULL DEFAULT '0' COMMENT '是否删除：0未删除 1已删除',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='自有库存表';

-- ----------------------------
-- Records of stock
-- ----------------------------
INSERT INTO `stock` VALUES ('1', '001', '1', '中国移动', '100.00', '库房', '100', '0', '0', '2018-08-22 13:57:02', '2018-08-20 20:39:15');
INSERT INTO `stock` VALUES ('2', '002', '1', '中国移动', '1.00', '库房', '3', '0', '0', '2018-08-22 13:57:02', '2018-08-20 20:46:26');
INSERT INTO `stock` VALUES ('3', '003', '1', '中国移动', '15.00', '库房', '10', '0', '0', '2018-08-24 09:47:12', '2018-08-24 09:47:12');
INSERT INTO `stock` VALUES ('4', '004', '3', '中国电信', '12.90', '库房', '10', '0', '0', '2018-08-26 10:48:25', '2018-08-22 10:33:16');

-- ----------------------------
-- Table structure for supplier
-- ----------------------------
DROP TABLE IF EXISTS `supplier`;
CREATE TABLE `supplier` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '供应商名称',
  `mobile` varchar(255) NOT NULL DEFAULT '' COMMENT '供应商电话',
  `telephone` varchar(255) NOT NULL DEFAULT '' COMMENT '供应商座机',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT '供应商邮箱',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `is_deleted` int(11) NOT NULL DEFAULT '0' COMMENT '是否删除：0未删除 1已删除',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='供应商';

-- ----------------------------
-- Records of supplier
-- ----------------------------
INSERT INTO `supplier` VALUES ('1', '中国移动', '13888888880', '010-8188484', '138@139.com', '0', '0', '2018-08-22 13:57:02', '2018-08-20 19:49:57');
INSERT INTO `supplier` VALUES ('2', '中国联通', '13100000000', '010-95958521', '131@131.com', '0', '0', '2018-08-21 12:59:13', '2018-08-21 12:59:13');
INSERT INTO `supplier` VALUES ('3', '中国电信', '17000000000', '010-45645621', '170@170.com', '0', '0', '2018-08-21 14:07:21', '2018-08-21 14:07:21');
INSERT INTO `supplier` VALUES ('4', '中国石化', '13562457485', '010-58414521', 'qwer@126.com', '0', '0', '2018-08-26 11:01:21', '2018-08-26 11:01:21');
