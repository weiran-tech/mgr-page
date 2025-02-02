<!-- 页面标签 -->
<div class="layadmin-pagetabs" id="LAY_app_tabs">
    <div class="layui-icon layadmin-tabs-control layui-icon-prev" ew-event="leftPage"></div>
    <div class="layui-icon layadmin-tabs-control layui-icon-next" ew-event="rightPage"></div>
    <div class="layui-icon layadmin-tabs-control layui-icon-down">
        <ul class="layui-nav layadmin-tabs-select"
            lay-filter="layadmin-pagetabs-nav">
            <li class="layui-nav-item" lay-unselect>
                <a href="#"></a>
                <dl class="layui-nav-child layui-anim-fadein">
                    <dd ew-event="closeThisTabs">
                        <a href="#">关闭当前标签页</a>
                    </dd>
                    <dd ew-event="closeOtherTabs">
                        <a href="#">关闭其它标签页</a>
                    </dd>
                    <dd ew-event="closeAllTabs">
                        <a href="#">关闭全部标签页</a>
                    </dd>
                </dl>
            </li>
        </ul>
    </div>
    <div class="layui-tab"
         lay-unauto
         lay-allowclose="true"
         lay-filter="layadmin-layout-tabs">
        <ul class="layui-tab-title" id="LAY_app_tabsheader" style="border-bottom: none!important;"></ul>
    </div>
</div>