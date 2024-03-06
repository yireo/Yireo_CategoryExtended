<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Model\Category\Attribute\Source;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class CmsPage extends AbstractSource
{
    protected $pageFactory;

    protected $pageHelper;

    public function __construct(\Magento\Cms\Model\PageFactory $pageFactory, \Magento\Cms\Helper\Page $pageHelper)
    {
        $this->pageFactory = $pageFactory;

        $this->pageHelper = $pageHelper;
    }

    public function getAllOptions($withEmpty = true)
    {
        $page = $this->pageFactory->create();

        $options = [];

        foreach($page->getCollection() as $item)
        {
            $options[] = ['value' => $item->getId(), 'label' => $item->getTitle()];
        }

        array_unshift($options, ['value' => '', 'label' => __('Please select a static page.')]);

        return $options;
    }
}
