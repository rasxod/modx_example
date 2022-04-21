<?php
/**
 * на основании https://gist.github.com/pavel-one/edfb408d25673f0c47885d75bfe5429d
 * плагин выбирает все катогрии TV и создает из них закладки
 * 
*/
$eventName = $modx->event->name;
switch($eventName) {
    case 'OnDocFormPrerender':
        if( $resource && $resource->get('template') == 1 || !$resource){
            $tv_ids = $modx->getCollection('modTemplateVarTemplate', ['templateid' => $resource->get('template')]);
            foreach ($tv_ids as $key => $ti) {
                $js_tab = [];
                $_tv = $modx->getObject('modTemplateVar', ['id' => $ti->get('tmplvarid')]);
                if ($_tv != null) {
                    $cat = $modx->getObject('modCategory', ['id' => $_tv->get('category')]);
                    if ($cat != null) {
                        $generator = $modx->newObject('modResource');
                            $js_tab[$generator->cleanAlias($cat->get('category'))]['tab'] = 'MODx.addTab("modx-resource-tabs",{title:"'.$cat->get('category').'",id:"'.$generator->cleanAlias($cat->get('category')).'"});';
                            $js_tab[$generator->cleanAlias($cat->get('category'))]['tv'][] = 'MODx.moveTV(["tv'.$ti->get('tmplvarid').'"], "'.$generator->cleanAlias($cat->get('category')).'");
                        ';
                    }
                }
            }
            $js_add = '';
            foreach ($js_tab as $key => $jst) {
                $js_add .= $jst['tab'].implode(' ', $jst['tv']);
            }

            $modx->regClientStartupHTMLBlock('<script>
        Ext.onReady(function() {
            	
             // Добавляем вкладку
            '.$js_add.'

        });
    </script>');
        }
        break;
}
