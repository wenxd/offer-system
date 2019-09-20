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
CREATE TABLE `cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `goods_id` int(11) NOT NULL DEFAULT '0' COMMENT '商品ID',
  `inquiry_id` int(11) NOT NULL DEFAULT '0' COMMENT '询价id',
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '类型：0最新 1优选 2库存',
  `number` int(11) NOT NULL DEFAULT '0' COMMENT '购买数量',
  `quotation_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '报价价格',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='购物车';


-- ----------------------------
-- Table structure for inquiry
-- ----------------------------
DROP TABLE IF EXISTS `inquiry`;
CREATE TABLE `inquiry` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `good_id` varchar(255) NOT NULL DEFAULT '' COMMENT '零件ID',
  `supplier_id` int(11) NOT NULL DEFAULT '0' COMMENT '供应商ID',
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
DROP TABLE IF EXISTS `order_inquiry`;
CREATE TABLE `order_inquiry` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `customer_id` int(11) NOT NULL DEFAULT '0' COMMENT '客户ID',
  `order_id` varchar(255) NOT NULL DEFAULT '' COMMENT '订单编号',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `quote_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '咨询价格',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `record_ids` text NOT NULL COMMENT '询价id列表 json',
  `stocks` varchar(255) NOT NULL DEFAULT '' COMMENT '库存id列表 json',
  `status` int(11) NOT NULL COMMENT '是否询价：0未询价 1已询价',
  `is_deleted` int(11) NOT NULL DEFAULT '0' COMMENT '是否删除：0未删除 1已删除',
  `provide_date` datetime DEFAULT NULL COMMENT '供货日期',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='询价单';

-- ----------------------------
-- Table structure for order_quote
-- ----------------------------
DROP TABLE IF EXISTS `order_quote`;
CREATE TABLE `order_quote` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `customer_id` int(11) NOT NULL DEFAULT '0' COMMENT '客户ID',
  `order_id` varchar(255) NOT NULL DEFAULT '' COMMENT '订单编号',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `quote_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '咨询价格',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `record_ids` text NOT NULL COMMENT '报价id列表 json',
  `stocks` varchar(255) NOT NULL DEFAULT '' COMMENT '库存id列表 json',
  `is_deleted` int(11) NOT NULL DEFAULT '0' COMMENT '是否删除：0未删除 1已删除',
  `provide_date` datetime DEFAULT NULL COMMENT '供货日期',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='报价单';

-- ----------------------------
-- Table structure for stock
-- ----------------------------
DROP TABLE IF EXISTS `stock`;
CREATE TABLE `stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `good_id` varchar(255) NOT NULL DEFAULT '' COMMENT '零件ID',
  `supplier_id` int(11) NOT NULL DEFAULT '0' COMMENT '供应商ID',
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='供应商';

CREATE TABLE `goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `goods_number` varchar(255) NOT NULL DEFAULT '' COMMENT '零件编号',
  `description` varchar(512) NOT NULL DEFAULT '' COMMENT '中文描述',
  `description_en` varchar(255) NOT NULL DEFAULT '' COMMENT '英文描述',
  `original_company` varchar(255) NOT NULL DEFAULT '' COMMENT '原厂家',
  `original_company_remark` varchar(255) NOT NULL DEFAULT '' COMMENT '原厂家备注',
  `unit` varchar(255) NOT NULL DEFAULT '' COMMENT '单位',
  `technique_remark` varchar(255) NOT NULL DEFAULT '' COMMENT '技术备注',
  `is_process` int(11) NOT NULL DEFAULT '0' COMMENT '是否加工',
  `img_id` varchar(255) NOT NULL DEFAULT '' COMMENT '图纸',
  `is_special` int(11) NOT NULL DEFAULT '0' COMMENT '是否特制 0不是 1是',
  `is_nameplate` int(11) NOT NULL DEFAULT '0' COMMENT '是否铭牌 0不是  1是',
  `nameplate_img_id` varchar(255) NOT NULL DEFAULT '' COMMENT '铭牌照片',
  `is_deleted` int(11) NOT NULL DEFAULT '0' COMMENT '是否删除：0未删除 1已删除',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='零件表';

CREATE TABLE `customer` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '客户名称',
  `short_name` varchar(255) NOT NULL DEFAULT '' COMMENT '客户名称简写',
  `mobile` varchar(255) NOT NULL DEFAULT '' COMMENT '客户电话',
  `company_telephone` varchar(255) NOT NULL DEFAULT '' COMMENT '公司电话',
  `company_fax` varchar(255) NOT NULL DEFAULT '' COMMENT '公司传真',
  `company_address` varchar(255) NOT NULL DEFAULT '' COMMENT '公司地址',
  `company_email` varchar(255) NOT NULL DEFAULT '' COMMENT '公司邮箱',
  `company_contacts` varchar(255) NOT NULL DEFAULT '' COMMENT '公司联系人',
  `is_deleted` int(11) NOT NULL DEFAULT '0' COMMENT '是否删除：0未删除 1已删除',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='客户表';

CREATE TABLE `md5_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bucket` varchar(255) NOT NULL DEFAULT '' COMMENT '资源bucket',
  `file_value` varchar(255) NOT NULL DEFAULT '' COMMENT '文件md5_file值',
  `file_path` varchar(255) NOT NULL DEFAULT '' COMMENT '文件的存储名称及路径',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `bucket` (`bucket`,`file_value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='图片md5验证表';

CREATE TABLE `order_inquiry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inquiry_sn` varchar(255) NOT NULL DEFAULT '' COMMENT '询价单号',
  `order_id` int(11) NOT NULL DEFAULT '0' COMMENT '订单ID',
  `end_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '询价截止时间',
  `is_inquiry` int(11) NOT NULL DEFAULT '0' COMMENT '是否询价：0未询价 1已询价',
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '询价员ID',
  `is_deleted` int(11) NOT NULL DEFAULT '0' COMMENT '是否删除：0未删除 1已删除',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='询价单';

CREATE TABLE `inquiry_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inquiry_sn` varchar(255) NOT NULL DEFAULT '' COMMENT '询价单号',
  `goods_id`   int(11) NOT NULL DEFAULT '0' COMMENT '零件ID',
  `is_deleted` int(11) NOT NULL DEFAULT '0' COMMENT '是否删除：0未删除 1已删除',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB COMMENT='询价单号与零件ID对应表';

ALTER TABLE `goods` ADD COLUMN `device_info` VARCHAR(510) NOT NULL DEFAULT '' COMMENT '设备信息 json存储';

CREATE TABLE `system_config` (
  `id`         int(11) NOT NULL AUTO_INCREMENT,
  `title`      varchar(255) NOT NULL DEFAULT '' COMMENT '配置名称',
  `value`      varchar(255) NOT NULL DEFAULT '' COMMENT '配置参数值',
  `is_deleted` int(11) NOT NULL DEFAULT '0' COMMENT '是否删除：0未删除 1已删除',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB COMMENT='系统配置';

ALTER TABLE `goods` ADD COLUMN `material` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '材质';
ALTER TABLE `inquiry_goods` ADD COLUMN `serial` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '序号' after `number`;

CREATE TABLE `system_notice` (
  `id`             int(11)      NOT NULL AUTO_INCREMENT,
  `admin_id`       int(11)      NOT NULL DEFAULT '0'  COMMENT '后端用户ID',
  `content`        varchar(255) NOT NULL DEFAULT ''   COMMENT '通知详情',
  `is_read`        tinyint(2)   NOT NULL DEFAULT '0'  COMMENT '是否已读：0未读 1已读',
  `is_deleted`     tinyint(2)   NOT NULL DEFAULT '0'  COMMENT '是否删除：0未删除 1已删除',
  `notice_at`      datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '通知时间',
  `updated_at`     datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `created_at`     datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB COMMENT='系统通知';

ALTER TABLE `inquiry` ADD COLUMN `inquiry_goods_id` int(11) NOT NULL DEFAULT '0'  COMMENT '询价零件表ID' after `order_inquiry_id`;
ALTER TABLE `inquiry` ADD COLUMN `number` int(11) NOT NULL DEFAULT '0'  COMMENT '询价数量' after `tax_rate`;
ALTER TABLE `inquiry` ADD COLUMN `all_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '未税总价' after `tax_rate`;
ALTER TABLE `inquiry` ADD COLUMN `all_tax_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '含税总价' after `tax_rate`;

ALTER TABLE `final_goods` ADD COLUMN `serial` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '序号' after `goods_id`;

ALTER TABLE `order_final` ADD COLUMN `is_quote` tinyint(2) NOT NULL DEFAULT '0'  COMMENT '是否生成报价单 0否 1是' after `agreement_date`;

ALTER TABLE `order_quote` ADD COLUMN `quote_ratio` decimal(4,2) NOT NULL DEFAULT '0.00' COMMENT '报价系数' after `is_quote`;
ALTER TABLE `order_quote` ADD COLUMN `delivery_ratio` decimal(4,2) NOT NULL DEFAULT '0.00' COMMENT '货期系数' after `is_quote`;
ALTER TABLE `order_quote` ADD COLUMN `quote_at` NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '报价时间';
ALTER TABLE `order_quote` ADD COLUMN `quote_only_one` TINYINT NOT NULL DEFAULT '0' COMMENT '某一订单下多个报价单 是否只有此报价单生成收入合同  0否  1是';
ALTER TABLE `order_quote` ADD COLUMN `is_send` TINYINT NOT NULL DEFAULT '0' COMMENT '是否发送报价单  0否  1是';
ALTER TABLE `order_quote` ADD COLUMN `customer_id` int(11) NOT NULL DEFAULT '0'  COMMENT '客户ID';

ALTER TABLE `quote_goods` ADD COLUMN `serial` varchar(255) NOT NULL DEFAULT '' COMMENT '序号';
ALTER TABLE `quote_goods` ADD COLUMN `tax_rate` decimal(4,2) NOT NULL DEFAULT '0.00' COMMENT '税率';
ALTER TABLE `quote_goods` ADD COLUMN `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '未税单价';
ALTER TABLE `quote_goods` ADD COLUMN `tax_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '含税单价';
ALTER TABLE `quote_goods` ADD COLUMN `all_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '未税总价';
ALTER TABLE `quote_goods` ADD COLUMN `all_tax_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '含税总价';
ALTER TABLE `quote_goods` ADD COLUMN `quote_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '报价未税单价';
ALTER TABLE `quote_goods` ADD COLUMN `quote_tax_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '报价含税单价';
ALTER TABLE `quote_goods` ADD COLUMN `quote_all_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '报价未税总价';
ALTER TABLE `quote_goods` ADD COLUMN `quote_all_tax_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '报价含税总价';
ALTER TABLE `quote_goods` ADD COLUMN `delivery_time` decimal(10,1) NOT NULL DEFAULT '0.0' COMMENT '货期（周）';
ALTER TABLE `quote_goods` ADD COLUMN `quote_delivery_time` decimal(10,1) NOT NULL DEFAULT '0.0' COMMENT '报价货期（周）';

ALTER TABLE `agreement_goods` ADD COLUMN `serial` varchar(255) NOT NULL DEFAULT '' COMMENT '序号';
ALTER TABLE `agreement_goods` ADD COLUMN `tax_rate` decimal(4,2) NOT NULL DEFAULT '0.00' COMMENT '税率';
ALTER TABLE `agreement_goods` ADD COLUMN `all_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '未税总价';
ALTER TABLE `agreement_goods` ADD COLUMN `all_tax_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '含税总价';
ALTER TABLE `agreement_goods` ADD COLUMN `quote_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '报价未税单价';
ALTER TABLE `agreement_goods` ADD COLUMN `quote_tax_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '报价含税单价';
ALTER TABLE `agreement_goods` ADD COLUMN `quote_all_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '报价未税总价';
ALTER TABLE `agreement_goods` ADD COLUMN `quote_all_tax_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '报价含税总价';
ALTER TABLE `agreement_goods` ADD COLUMN `inquiry_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '询价员ID';
ALTER TABLE `agreement_goods` ADD COLUMN `is_out` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否出库';
ALTER TABLE `agreement_goods` ADD COLUMN `quote_delivery_time` decimal(10,1) NOT NULL DEFAULT '0.0' COMMENT '报价货期（周）';

ALTER TABLE `supplier` ADD COLUMN `short_name` varchar(255) NOT NULL DEFAULT '' COMMENT '供应商简称';

ALTER TABLE `purchase_goods` ADD COLUMN `serial` varchar(255) NOT NULL DEFAULT '' COMMENT '序号';
ALTER TABLE `purchase_goods` ADD COLUMN `tax_rate` decimal(4,2) NOT NULL DEFAULT '0.00' COMMENT '税率';
ALTER TABLE `purchase_goods` ADD COLUMN `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '未税单价';
ALTER TABLE `purchase_goods` ADD COLUMN `all_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '未税总价';
ALTER TABLE `purchase_goods` ADD COLUMN `all_tax_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '含税总价';
ALTER TABLE `purchase_goods` ADD COLUMN `fixed_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '修改后的未税单价';
ALTER TABLE `purchase_goods` ADD COLUMN `fixed_tax_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '修改后的含税单价';
ALTER TABLE `purchase_goods` ADD COLUMN `fixed_all_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '修改后的未税总价';
ALTER TABLE `purchase_goods` ADD COLUMN `fixed_all_tax_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '修改后的含税总价';
ALTER TABLE `purchase_goods` ADD COLUMN `fixed_number` int(11) NOT NULL DEFAULT '0' COMMENT '修改后的数量';
ALTER TABLE `purchase_goods` ADD COLUMN `inquiry_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '询价员ID';
ALTER TABLE `purchase_goods` ADD COLUMN `reason` varchar(255) NOT NULL DEFAULT '' COMMENT '驳回原因';
ALTER TABLE `purchase_goods` ADD COLUMN `apply_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '审核状态 0无 1审核中 2审核通过 3被驳回';
ALTER TABLE `purchase_goods` ADD COLUMN `delivery_time`decimal(10,1) NOT NULL DEFAULT '0.0' COMMENT '采购货期（周）';

CREATE TABLE `order_payment` (
  `id`                  int(11) NOT NULL AUTO_INCREMENT,
  `payment_sn`          varchar(255) NOT NULL DEFAULT '' COMMENT '支出合同单号',
  `order_id`            int(11) NOT NULL DEFAULT '0' COMMENT '订单ID',
  `order_purchase_id`   int(11) NOT NULL DEFAULT '0' COMMENT '采购订单ID',
  `order_purchase_sn`   varchar(255) NOT NULL DEFAULT '' COMMENT '采购单号',
  `goods_info`          varchar(512) NOT NULL DEFAULT '' COMMENT '零件信息 json，包括ID',
  `payment_at`          datetime                     COMMENT '支付时间',
  `is_payment`          int(11) NOT NULL DEFAULT '0' COMMENT '是否支付：0未 1已',
  `admin_id`            int(11) NOT NULL DEFAULT '0' COMMENT '采购员ID',
  `purchase_status`     tinyint(4) NOT NULL DEFAULT '0' COMMENT '采购状态 0请审核 1审核通过 2驳回',
  `updated_at`          datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `created_at`          datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='支出合同单';

CREATE TABLE `payment_goods` (
  `id`                  int(11) NOT NULL AUTO_INCREMENT,
  `order_id`            int(11) NOT NULL DEFAULT '0' COMMENT '订单ID',
  `order_payment_id`    int(11) NOT NULL DEFAULT '0' COMMENT '支出合同订单ID',
  `order_payment_sn`    varchar(255) NOT NULL DEFAULT '' COMMENT '支出合同订单号',
  `order_purchase_id`   int(11) NOT NULL DEFAULT '0' COMMENT '采购订单ID',
  `order_purchase_sn`   varchar(255) NOT NULL DEFAULT '' COMMENT '采购订单号',
  `purchase_goods_id`   int(11) NOT NULL DEFAULT '0' COMMENT '支出合同单商品主键',
  `serial`              varchar(255) NOT NULL DEFAULT '' COMMENT '序号',
  `goods_id`            int(11) NOT NULL DEFAULT '0' COMMENT '零件ID',
  `type`                int(11) NOT NULL DEFAULT '0' COMMENT '关联类型  0询价  1库存',
  `relevance_id`        int(11) NOT NULL DEFAULT '0' COMMENT '关联ID（询价或库存）',
  `number`              int(11) NOT NULL DEFAULT '0' COMMENT '采购数量',
  `tax_rate`            decimal(4,2) NOT NULL DEFAULT '0.00' COMMENT '税率',
  `price`               decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '未税单价',
  `tax_price`           decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '含税总价',
  `all_price`           decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '未税总价',
  `all_tax_price`       decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '含税总价',
  `fixed_price`         decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '修改后的未税单价',
  `fixed_tax_price`     decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '修改后的含税单价',
  `fixed_all_price`     decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '修改后的未税总价',
  `fixed_all_tax_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '修改后的含税总价',
  `fixed_number`        int(11) NOT NULL DEFAULT '0' COMMENT '修改后的数量',
  `inquiry_admin_id`    int(11) NOT NULL DEFAULT '0' COMMENT '询价员ID',
  `updated_at`          datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `created_at`          datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='支出合同单与零件ID对应表';

ALTER TABLE `order_purchase` ADD COLUMN `payment_sn` varchar(255) NOT NULL DEFAULT '' COMMENT '支出合同单号';
ALTER TABLE `order_purchase` ADD COLUMN `is_complete` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否完成所有的确认';

ALTER TABLE `order_payment` ADD COLUMN `is_verify`         tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否审核 0未 1是';
ALTER TABLE `order_payment` ADD COLUMN `is_stock`          tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否入库  0否 1是';
ALTER TABLE `order_payment` ADD COLUMN `is_advancecharge`  tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否预付款 0否 1是';
ALTER TABLE `order_payment` ADD COLUMN `is_bill`           tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否接收发票 0否  1是';
ALTER TABLE `order_payment` ADD COLUMN `stock_at`          datetime  COMMENT '入库时间';
ALTER TABLE `order_payment` ADD COLUMN `advancecharge_at`  datetime  COMMENT '预付款时间';
ALTER TABLE `order_payment` ADD COLUMN `bill_at`           datetime  COMMENT '收到发票时间';
ALTER TABLE `order_payment` ADD COLUMN `financial_remark`  varchar(255) NOT NULL DEFAULT '' COMMENT '财务备注';
ALTER TABLE `order_payment` ADD COLUMN `is_complete` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否完成所有的操作 用于财务管理是否展示，0默认财务展示 1都完成就不展示了';
ALTER TABLE `order_payment` ADD COLUMN `payment_price`  decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '支出订单总金额';
ALTER TABLE `order_payment` ADD COLUMN `remain_price`   decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '支出订单剩余金额 总金额减去预付款金额';
ALTER TABLE `order_payment` ADD COLUMN `payment_ratio`  int(11) NOT NULL DEFAULT '0' COMMENT '预付款比例';
ALTER TABLE `order_payment` ADD COLUMN `take_time`      datetime  COMMENT '收货时间 = 支出合同签订日加最长货期';
ALTER TABLE `order_payment` ADD COLUMN `is_agreement`   tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否生成合同 0否 1是';
ALTER TABLE `order_payment` ADD COLUMN `apply_reason`   varchar(255) NOT NULL DEFAULT '' COMMENT '采购申请支出备注';
ALTER TABLE `order_payment` ADD COLUMN `agreement_at`   datetime  COMMENT '生成支出合同的时间 支出合同签订时间';
ALTER TABLE `order_payment` ADD COLUMN `delivery_date`  datetime  COMMENT '支出合同交货时间';
ALTER TABLE `order_payment` ADD COLUMN `supplier_id`    int(11) NOT NULL DEFAULT '0' COMMENT '供应商ID';
ALTER TABLE `order_payment` ADD COLUMN `financial_admin_id`    int(11) NOT NULL DEFAULT '0' COMMENT '财务用户ID';
ALTER TABLE `order_payment` ADD COLUMN `stock_admin_id`    int(11) NOT NULL DEFAULT '0' COMMENT '库管用户ID';

ALTER TABLE `purchase_goods` ADD COLUMN `is_stock` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否入库  0否 1是';

ALTER TABLE `order_agreement` ADD COLUMN `is_advancecharge`  tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否预收款 0否 1是';
ALTER TABLE `order_agreement` ADD COLUMN `is_payment`        tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否收全款 0否 1是';
ALTER TABLE `order_agreement` ADD COLUMN `is_bill`           tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否开发票 0否  1是';
ALTER TABLE `order_agreement` ADD COLUMN `is_stock`          tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否出库  0否 1是';
ALTER TABLE `order_agreement` ADD COLUMN `advancecharge_at`  datetime  COMMENT '预收款时间';
ALTER TABLE `order_agreement` ADD COLUMN `payment_at`        datetime  COMMENT '收全款时间';
ALTER TABLE `order_agreement` ADD COLUMN `bill_at`           datetime  COMMENT '开发票时间';
ALTER TABLE `order_agreement` ADD COLUMN `stock_at`          datetime  COMMENT '出库时间';
ALTER TABLE `order_agreement` ADD COLUMN `is_complete`       tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否完成所有的操作 用于财务管理是否展示，0默认财务展示 1都完成就不展示了',
ALTER TABLE `order_agreement` ADD COLUMN `financial_remark`  varchar(255) NOT NULL DEFAULT '' COMMENT '财务备注';
ALTER TABLE `order_agreement` ADD COLUMN `is_complete`       tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否完成所有的操作 用于财务管理是否展示，0默认财务展示 1都完成就不展示了';
ALTER TABLE `order_agreement` ADD COLUMN `sign_date`         datetime  COMMENT '合同签订时间';
ALTER TABLE `order_agreement` ADD COLUMN `is_instock`        tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否入库  0否 1是';
ALTER TABLE `order_agreement` ADD COLUMN `customer_id`       int(11) NOT NULL DEFAULT '0'  COMMENT '客户ID';
ALTER TABLE `order_agreement` ADD COLUMN `payment_price`  decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '收入订单总金额';
ALTER TABLE `order_agreement` ADD COLUMN `remain_price`   decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '收入订单剩余金额 总金额减去预收款金额';
ALTER TABLE `order_agreement` ADD COLUMN `payment_ratio`  int(11) NOT NULL DEFAULT '0' COMMENT '预收款比例';


ALTER TABLE `stock_log` ADD COLUMN  `order_agreement_id` int(11) NOT NULL DEFAULT '0' COMMENT '收入合同单ID';
ALTER TABLE `stock_log` ADD COLUMN  `agreement_sn`       varchar(255) NOT NULL DEFAULT '' COMMENT '收入合同单号';
ALTER TABLE `stock_log` ADD COLUMN  `remark`             varchar(255) NOT NULL DEFAULT '' COMMENT '出入库备注';
ALTER TABLE `stock_log` ADD COLUMN  `order_purchase_id` int(11) NOT NULL DEFAULT '0' COMMENT '采购单ID';
ALTER TABLE `stock_log` ADD COLUMN  `purchase_sn` int(11) NOT NULL DEFAULT '0' COMMENT '采购单ID';
ALTER TABLE `stock_log` ADD COLUMN  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '操作人ID';
ALTER TABLE `stock_log` ADD COLUMN  `is_manual` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否手动 0否 1是';
ALTER TABLE `stock_log` ADD COLUMN  `direction` varchar(255) NOT NULL DEFAULT '' COMMENT '去向';
ALTER TABLE `stock_log` ADD COLUMN  `customer_id` int(11) NOT NULL DEFAULT '0' COMMENT '客户id';
ALTER TABLE `stock_log` ADD COLUMN  `region` varchar(255) NOT NULL DEFAULT ''COMMENT '区块';
ALTER TABLE `stock_log` ADD COLUMN  `plat_name` varchar(255) NOT NULL DEFAULT ''COMMENT '平台名称';


CREATE TABLE `temp_order_inquiry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_ids` TEXT     COMMENT '零件id 多个用,分开',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='临时选择商品生成询价单';

CREATE TABLE `temp_order_goods` (
  `id`          int(11) NOT NULL AUTO_INCREMENT,
  `serial`      varchar(255) NOT NULL DEFAULT '' COMMENT '序号',
  `goods_id`    int(11) NOT NULL DEFAULT '0' COMMENT '零件ID',
  `number`      int(11) NOT NULL DEFAULT '0' COMMENT '数量',
  `token`       varchar(255) NOT NULL DEFAULT '' COMMENT '每次上传的一批零件统一为一个token',
  `updated_at`  datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `created_at`  datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='临时选择商品生成订单';

CREATE TABLE `temp_not_goods` (
  `id`           int(11)  NOT NULL AUTO_INCREMENT,
  `goods_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '零件号',
  `updated_at`   datetime   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `created_at`   datetime   NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单添加零件系统不存在的零件';

ALTER TABLE `order_final` ADD COLUMN `customer_id` int(11) NOT NULL DEFAULT '0' COMMENT '客户ID';
ALTER TABLE `order_final` ADD COLUMN `is_agreement` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否生成收入合同 0否 1是';

ALTER TABLE `payment_goods` ADD COLUMN  `is_quality` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否质检 0否 1是';
ALTER TABLE `payment_goods` ADD COLUMN `supplier_id` int(11) NOT NULL DEFAULT '0' COMMENT '供应商ID';
ALTER TABLE `payment_goods` ADD COLUMN `delivery_time` decimal(10,1) NOT NULL DEFAULT '0.0' COMMENT '采购货期（周）';

CREATE TABLE `temp_not_stock` (
  `id`           int(11)  NOT NULL AUTO_INCREMENT,
  `goods_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '零件号',
  `updated_at`   datetime   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `created_at`   datetime   NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='手动出库库存不够的零件';


ALTER TABLE `competitor_goods` ADD COLUMN  `stock_number`   int(11) NOT NULL DEFAULT '0' COMMENT '库存数量';
ALTER TABLE `competitor_goods` ADD COLUMN  `delivery_time`  int(11) NOT NULL DEFAULT '0' COMMENT '货期（周）'
ALTER TABLE `competitor_goods` ADD COLUMN  `all_price`      decimal(10,2) NOT NULL DEFAULT '0' COMMENT '未税总价'
ALTER TABLE `competitor_goods` ADD COLUMN  `all_tax_price`      decimal(10,2) NOT NULL DEFAULT '0' COMMENT '含税总价'

ALTER TABLE `inquiry` ADD COLUMN  `is_upload` int(11) NOT NULL DEFAULT '0' COMMENT '是否导入 0否 1是'
