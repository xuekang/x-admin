<?php
declare (strict_types = 1);

// 全局常量

// 排序默认值
define('ORDER_VALUE', 1000000);

//分页参数默认值
define('PAGE_START', 1);
define('PAGE_LIMIT', 10);
define('PAGE_ORDER', 'order_value asc,id asc');
define('QUERY_LIMIT_MAX', 1000);    //全量记录查询上限

//默认系统用户id
define('DEFAULT_USER_ID', 0);
//默认系统用户中文名
define('DEFAULT_USER_REAL_NAME', '系统');

//默认系统客户端编码
define('DEFAULT_CLIENT', 'pc');

//用户设备类型
define('CLIENT_PC', 'pc');
define('CLIENT_MOBILE', 'mobile');





//流程动作类型
define('FLOW_ACTION_TYPE_CREATE', 'flow_create');//创建
define('FLOW_ACTION_TYPE_COMPLETE', 'flow_complete');//完成
define('FLOW_ACTION_TYPE_CLOSE', 'flow_close');//终止
define('FLOW_ACTION_TYPE_SUSPEND', 'flow_suspend');//冻结
define('FLOW_ACTION_TYPE_RECOVERY', 'flow_recovery');//恢复
define('FLOW_ACTION_TYPE_WAKE', 'flow_wake');//唤醒

//节点动作类型
define('NODE_ACTION_TYPE_CREATE', 'node_create');//创建
define('NODE_ACTION_TYPE_PASS', 'node_pass');//通过
define('NODE_ACTION_TYPE_REFUSE', 'node_refuse');//回退
define('NODE_ACTION_TYPE_CLOSE', 'node_close');//终止
define('NODE_ACTION_TYPE_SUSPEND', 'node_suspend');//冻结

//动作点
define('ACTION_POINT_BEFORE', 1);//之前
define('ACTION_POINT_AFTER', 2);//之后

//进程状态类型(流程状态)
define('PROCESS_STATUS_CREATE', 1);//创建
define('PROCESS_STATUS_IN', 2);//流程中
define('PROCESS_STATUS_COMPLETE', 3);//完成
define('PROCESS_STATUS_CLOSE', 4);//终止
define('PROCESS_STATUS_SUSPEND', 5);//冻结

//线程状态类型(节点状态)
define('THREAD_STATUS_CREATE', 1);//创建
define('THREAD_STATUS_IN', 2);//处理中
define('THREAD_STATUS_PASS', 3);//通过
define('THREAD_STATUS_CLOSE', 4);//终止
define('THREAD_STATUS_REFUSE', 5);//回退
define('THREAD_STATUS_SUSPEND', 6);//冻结

//节点类型
define('NODE_TYPE_NORMAL', 1);//通用节点
define('NODE_TYPE_START', 2);//开始节点
define('NODE_TYPE_END', 3);//结束节点
define('NODE_TYPE_CONDITION', 4);//条件节点
define('NODE_TYPE_SPLIT', 5);//并行开始节点
define('NODE_TYPE_AND_JOIN', 6);//并行全部结束
define('NODE_TYPE_OR_JOIN', 7);//并行任意结束

//线类型
define('LINE_TYPE_NORMAL', 1);//正常线
define('LINE_TYPE_REFUSE', 2);//回退线

//元素规则类型
define('ELE_RULE_TYPE_MUST', 1);//必须元素
define('ELE_RULE_TYPE_NO_MUST', 2);//非必须元素
define('ELE_RULE_TYPE_SHOW', 3);//显示元素
define('ELE_RULE_TYPE_HIDE', 4);//隐藏元素
define('ELE_RULE_TYPE_MAKE', 5);//生成元素
define('ELE_RULE_TYPE_UPDATE', 6);//更新元素
define('ELE_RULE_TYPE_SHOW_VALUE', 7);//元素显示值



//表单类型属性
define('FORM_TYPE_TEXT', 'text');
define('FORM_TYPE_NUMBER', 'number');
define('FORM_TYPE_MONEY', 'money');
define('FORM_TYPE_TEXTAREA', 'textarea');
define('FORM_TYPE_RICHTEXT', 'rich_text');
define('FORM_TYPE_SELECT', 'select');
define('FORM_TYPE_RADIO', 'radio');
define('FORM_TYPE_CHECKBOX', 'checkbox');
define('FORM_TYPE_IMG', 'img');
define('FORM_TYPE_FILE', 'file');
define('FORM_TYPE_VIDEO', 'video');
define('FORM_TYPE_DATE', 'date');
define('FORM_TYPE_TIME', 'time');
define('FORM_TYPE_DATETIME', 'datetime');
define('FORM_TYPE_DATARANGE', 'date_range');
define('FORM_TYPE_TIMERANGE', 'time_range');
define('FORM_TYPE_DATETIMERANGE', 'datetime_range');
define('FORM_TYPE_MAP', 'map');
define('FORM_TYPE_ADDR', 'addr');
define('FORM_TYPE_CASCADE', 'cascade');
define('FORM_TYPE_SELECTAJAX', 'select_ajax');
define('FORM_TYPE_CASCADEAJAX', 'cascade_ajax');


//权限类型
define('AUTH_TYPE_MENU', 'menu');
define('AUTH_TYPE_PAGE', 'page');
define('AUTH_TYPE_BUTTON', 'button');

