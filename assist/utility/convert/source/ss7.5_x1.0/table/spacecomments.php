<?php
/*
 *
 *  * Copyright 2012-2020 the original author or authors.
 *  *
 *  * Licensed under the Apache License, Version 2.0 (the "License");
 *  * you may not use this file except in compliance with the License.
 *  * You may obtain a copy of the License at
 *  *
 *  *      https://www.apache.org/licenses/LICENSE-2.0
 *  *
 *  * Unless required by applicable law or agreed to in writing, software
 *  * distributed under the License is distributed on an "AS IS" BASIS,
 *  * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  * See the License for the specific language governing permissions and
 *  * limitations under the License.
 *
 */

/**
 * DiscuzX Convert
 *
 * $Id: spacecomments.php 10469 2010-05-11 09:12:14Z monkey $
 */

$curprg = basename(__FILE__);

$table_source = $db_source->tablepre.'spacecomments';
$table_target = $db_target->tablepre.'portal_comment';

$limit = 150;
$nextid = 0;

$start = getgpc('start');

if($start == 0) {
	$db_target->query("TRUNCATE $table_target");
}

$query = $db_source->query("SELECT  * FROM $table_source WHERE cid>'$start' ORDER BY cid LIMIT $limit");
while ($rs = $db_source->fetch_array($query)) {

	$nextid = $rs['cid'];

	$setarr = array();
	$setarr['cid'] = $rs['cid'];
	$setarr['uid'] = $rs['authorid'];
	$setarr['username'] = $rs['author'];
	$setarr['aid'] = $rs['itemid'];
	$setarr['postip'] = $rs['ip'];
	$setarr['dateline'] = $rs['dateline'];
	$setarr['message'] = $rs['message'];

	$setarr  = daddslashes($setarr, 1);

	$data = implode_field_value($setarr, ',', db_table_fields($db_target, $table_target));

	$db_target->query("INSERT INTO $table_target SET $data");
}

if($nextid) {
	showmessage("继续转换数据表 ".$table_source." cid> $nextid", "index.php?a=$action&source=$source&prg=$curprg&start=$nextid");
}

?>