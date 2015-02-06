-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- 主机: 127.0.0.1
-- 生成日期: 2014 年 08 月 04 日 02:28
-- 服务器版本: 5.5.27
-- PHP 版本: 5.4.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `easy`
--

-- --------------------------------------------------------

--
-- 表的结构 `sys_access`
--

CREATE TABLE IF NOT EXISTS `sys_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL COMMENT '角色id',
  `node_id` int(11) NOT NULL COMMENT '节点id',
  PRIMARY KEY (`id`),
  KEY `fk_role_access` (`role_id`),
  KEY `fk_node_acess` (`node_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='结点权限访问表';

-- --------------------------------------------------------

--
-- 表的结构 `sys_node`
--

CREATE TABLE IF NOT EXISTS `sys_node` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `pid` int(11) NOT NULL COMMENT '父节点id',
  `name` varchar(32) NOT NULL COMMENT '节点名称',
  `title` varchar(32) NOT NULL COMMENT '节点标题',
  `level` tinyint(4) NOT NULL COMMENT '节点等级',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '节点状态',
  `created` int(11) NOT NULL COMMENT '创建时间',
  `updated` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='节点表';

-- --------------------------------------------------------

--
-- 表的结构 `sys_role`
--

CREATE TABLE IF NOT EXISTS `sys_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `pid` int(11) NOT NULL COMMENT '父角色id',
  `name` varchar(32) NOT NULL COMMENT '角色名称',
  `description` text NOT NULL COMMENT '角色描述',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '角色状态',
  `created` datetime NOT NULL COMMENT '创建时间',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='角色表';

-- --------------------------------------------------------

--
-- 表的结构 `sys_role_user`
--

CREATE TABLE IF NOT EXISTS `sys_role_user` (
  `role_id` int(11) NOT NULL COMMENT '角色id',
  `user_id` int(11) NOT NULL COMMENT '管理员id',
  KEY `fk_role_admin` (`role_id`),
  KEY `fk_admin_role` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员权限表';

-- --------------------------------------------------------

--
-- 表的结构 `sys_user`
--

CREATE TABLE IF NOT EXISTS `sys_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` char(32) NOT NULL,
  `password` varchar(32) NOT NULL COMMENT '登录密码',
  `remark` text NOT NULL COMMENT '管理员备注信息',
  `is_super` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否超级管理员',
  `is_active` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否启用',
  `last_login` datetime NOT NULL COMMENT '最后登录时间',
  `created` datetime NOT NULL COMMENT '创建时间',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='管理员表';

-- --------------------------------------------------------

--
-- 表的结构 `test_mytest`
--

CREATE TABLE IF NOT EXISTS `test_mytest` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `type` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- 限制导出的表
--

--
-- 限制表 `sys_access`
--
ALTER TABLE `sys_access`
  ADD CONSTRAINT `fk_node_acess` FOREIGN KEY (`node_id`) REFERENCES `sys_node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_role_acess` FOREIGN KEY (`role_id`) REFERENCES `sys_role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `sys_role_user`
--
ALTER TABLE `sys_role_user`
  ADD CONSTRAINT `fk_ar` FOREIGN KEY (`user_id`) REFERENCES `sys_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ra` FOREIGN KEY (`role_id`) REFERENCES `sys_role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
