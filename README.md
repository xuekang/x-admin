# X-ADMIN

> 运行环境要求 PHP7.2+，兼容 PHP8.1

## 主要新特性

- 完全由后端配置数据动态渲染前端路由，新增页面时只需新增后台数据配置，前端无需任何理路由配置
- 完全由后端配置数据前端权限控制，除了常规的菜单页面权限控制，更是细化到按钮权限控制
- 基于 RBAC 模型（role-based-access-control）的通用权限管理模块
- 通过后台配置数据自动生成常规的 CRUD 前端管理页面
- 通用流程设计和管理，支持行政办公和业务办公流程
- 通用流程报表查询
- 可按配置自动生表单、表格、按钮，可视化配置生成 crud 页面
- 后端接口自动生成可视化的接口文档

## 安装和使用

```
git clone git@github.com:xuekang/x-admin.git
```

如果需要更新框架使用

```
cd x-admin

composer install
```

启动内置服务器

```
php think run

php think run -H tp.com -p 80 //支持制定IP和端口访问
```
