<?php

class Shopware_Tests_Plugins_Core_MarketingAggregate_AbstractMarketing extends Enlight_Components_Test_Plugin_TestCase
{
    /**
     * @return Shopware_Components_SimilarShown
     */
    protected function SimilarShown() {
        return Shopware()->SimilarShown();
    }

    /**
     * @return Shopware_Components_TopSeller
     */
    protected function TopSeller() {
        return Shopware()->TopSeller();
    }

    /**
     * @return Shopware_Components_AlsoBought
     */
    protected function AlsoBought() {
        return Shopware()->AlsoBought();
    }

    /**
     * @return Enlight_Components_Db_Adapter_Pdo_Mysql
     */
    protected function Db() {
        return Shopware()->Db();
    }

    /**
     * @return sArticles
     */
    protected function Articles() {
        return Shopware()->Modules()->Articles();
    }

    /**
     * @return Shopware_Plugins_Core_MarketingAggregate_Bootstrap
     */
    protected function Plugin() {
        return Shopware()->Plugins()->Core()->MarketingAggregate();
    }


    public function setUp() {
        parent::setUp();
    }



    protected function assertArrayEquals(array $expected, array $result, array $properties)
    {
        foreach($properties as $property) {
            $this->assertEquals($expected[$property], $result[$property]);
        }
    }


    /**
     * Helper method to persist a given config value
     */
    protected function saveConfig($name, $value)
    {
        $shopRepository    = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop');
        $elementRepository = Shopware()->Models()->getRepository('Shopware\Models\Config\Element');
        $formRepository    = Shopware()->Models()->getRepository('Shopware\Models\Config\Form');

        $shop = $shopRepository->find($shopRepository->getActiveDefault()->getId());

        if (strpos($name, ':') !== false) {
            list($formName, $name) = explode(':', $name, 2);
        }

        $findBy = array('name' => $name);
        if (isset($formName)) {
            $form = $formRepository->findOneBy(array('name' => $formName));
            $findBy['form'] = $form;
        }

        /** @var $element Shopware\Models\Config\Element */
        $element = $elementRepository->findOneBy($findBy);

        // If the element is empty, the given setting does not exists. This might be the case for some plugins
        // Skip those values
        if (empty($element)) {
            return;
        }

        foreach ($element->getValues() as $valueModel) {
            Shopware()->Models()->remove($valueModel);
        }

        $values = array();
        // Do not save default value
        if ($value !== $element->getValue()) {
            $valueModel = new Shopware\Models\Config\Value();
            $valueModel->setElement($element);
            $valueModel->setShop($shop);
            $valueModel->setValue($value);
            $values[$shop->getId()] = $valueModel;
        }

        $element->setValues($values);
        Shopware()->Models()->flush($element);
    }

}