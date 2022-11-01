<?

namespace Journal;

use Its\Library\Iblock\Iblock as IB;

class CatalogEventHandler
{
    public static function SubscribeTableOnAfterAddHandler(\Bitrix\Main\Entity\Event $event)
    {
        $id = $event->getParameter("id");
        $fields = $event->getParameter("fields");
        if (!$id) {
            return true;
        }
        //добавление Имя и телефон в отдельный HL
        \Bitrix\Main\Loader::includeModule("highloadblock");
        $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList(
            ['filter' => ['=NAME' => 'CatalogSubscribeExtended']]
        )->fetch();

        if (!$hlblock) {
            return true;
        }

        $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();

        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        $name = $request->get('name');
        $phone = \Journal\Utils::preparePhoneNumber($request->get('phone'));

        //если выбрано "подписаться на новости"
        $subscribe_email_list = $request->get('subscribe_emailing_list');
        if ($subscribe_email_list == 'Y') {
            \Bitrix\Main\Loader::includeModule("its.sendpulse");
            $sendpulse = new \Its\Sendpulse\Sendpulse();
            $emails = array(
                array(
                    'email' => $fields['USER_CONTACT'],
                )
            );
            $resultSendPulse = $sendpulse->addEmails($emails);
        }

        $data = array(
            "UF_ID_SUBSCRIBE" => $id,
            "UF_NAME" => $name,
            "UF_PHONE" => $phone,
            "UF_ITEM_ID" => $fields['ITEM_ID'],
            "UF_USER_ID" => $fields['USER_ID'],
            "UF_SITE_ID" => $fields['SITE_ID'],
            "UF_EMAIL" => $fields['USER_CONTACT'],
            "UF_DATETIME" => $fields['DATE_FROM'],
        );
        $result = $entity_data_class::add($data);
    }
}
