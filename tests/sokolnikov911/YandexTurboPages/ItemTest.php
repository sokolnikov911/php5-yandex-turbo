<?php

namespace sokolnikov911\YandexTurboPages;

use Mockery;
use PHPUnit\Framework\TestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class ItemTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private $relatedItemsListInterface = '\sokolnikov911\YandexTurboPages\RelatedItemsList';

    public function testTitle()
    {
        $title = uniqid();
        $item = new Item();
        $this->assertSame($item, $item->title($title));
        $this->assertAttributeSame($title, 'title', $item);
    }

    public function testLink()
    {
        $link = uniqid();
        $item = new Item();
        $this->assertSame($item, $item->link($link));
        $this->assertAttributeSame($link, 'link', $item);
    }

    public function testTurboContent()
    {
        $turboContent = uniqid();
        $item = new Item();
        $this->assertSame($item, $item->turboContent($turboContent));
        $this->assertAttributeSame($turboContent, 'turboContent', $item);
    }

    public function testCategory()
    {
        $category = uniqid();
        $item = new Item();
        $this->assertSame($item, $item->category($category));
        $this->assertAttributeSame($category, 'category', $item);
    }

    public function testAuthor()
    {
        $author = uniqid();
        $item = new Item();
        $this->assertSame($item, $item->author($author));
        $this->assertAttributeSame($author, 'author', $item);
    }

    public function testPubDate()
    {
        $pubDate = mt_rand(1000000, 9999999);
        $item = new Item();
        $this->assertSame($item, $item->pubDate($pubDate));
        $this->assertAttributeSame($pubDate, 'pubDate', $item);
    }

    public function testAppendTo()
    {
        $item = new Item();
        $channel = new Channel();
        $this->assertSame($channel, $channel->addItem($item));
        $this->assertAttributeSame([$item], 'items', $channel);
    }

    public function testTurboAttributeEnabled()
    {
        $item = new Item(true);
        $this->assertAttributeSame(true, 'turbo', $item);
    }

    public function testTurboAttributeDisabled()
    {
        $item = new Item(false);
        $this->assertAttributeSame(false, 'turbo', $item);
    }

    public function testCdataTurboContent()
    {
        $content = '<div>content</div>';
        $pubDate = time();

        $item = new Item();;
        $this->assertSame($item, $item->pubDate($pubDate));
        $this->assertSame($item, $item->turboContent($content));
        $this->assertAttributeSame($content, 'turboContent', $item);

        $feed = new Feed();
        $channel = new Channel();
        $item->appendTo($channel);
        $channel->appendTo($feed);

        $expected = '<?xml version="1.0" encoding="UTF-8"?>
        <rss xmlns:yandex="http://news.yandex.ru" xmlns:media="http://search.yahoo.com/mrss/" xmlns:turbo="http://turbo.yandex.ru" version="2.0">
            <channel>
                <title/>
                <link/>
                <description/>
                <item turbo="true">
                    <title/>
                    <link/>
                    <turbo:content xmlns:turbo="http://turbo.yandex.ru"><![CDATA[' . $content . ']]></turbo:content>
                    <pubDate>' . date(DATE_RSS, $pubDate). '</pubDate>
                </item>
            </channel>
        </rss>';
        $this->assertXmlStringEqualsXmlString($expected, strval($feed));
    }

    public function testAddRelatedItemList()
    {
        $relatedItemsList = Mockery::mock($this->relatedItemsListInterface);
        $item = new Item();
        $this->assertSame($item, $item->addRelatedItemsList($relatedItemsList));
        $this->assertAttributeSame($relatedItemsList, 'relatedItemsList', $item);
    }

    public function testAsXML()
    {
        $data = $this->dataForXmlTests();

        $item = new Item();
        $item
            ->author($data['author'])
            ->pubDate($data['now'])
            ->title($data['title'])
            ->link($data['link'])
            ->turboContent($data['turboContent'])
            ->category($data['category']);

        $expect = '
        <item turbo="true">
            <title>' . $data['title'] . '</title>
            <link>' . $data['link'] . '</link>
            <turbo:content xmlns:turbo="http://turbo.yandex.ru"><![CDATA[' . $data['turboContent'] . ']]></turbo:content>
            <pubDate>' . $data['pubDate'] . '</pubDate>
            <category>' . $data['category'] . '</category>
            <author>' . $data['author'] . '</author>
        </item>
        ';

        $this->assertXmlStringEqualsXmlString($expect, $item->asXML()->asXML());
    }

    public function testAsXMLWithDisabledTurbo()
    {
        $data = $this->dataForXmlTests();

        $item = new Item(false);
        $item
            ->author($data['author'])
            ->pubDate($data['now'])
            ->title($data['title'])
            ->link($data['link'])
            ->turboContent($data['turboContent'])
            ->category($data['category']);

        $expect = '
        <item turbo="false">
            <title>' . $data['title'] . '</title>
            <link>' . $data['link'] . '</link>
            <turbo:content xmlns:turbo="http://turbo.yandex.ru"><![CDATA[' . $data['turboContent'] . ']]></turbo:content>
            <pubDate>' . $data['pubDate'] . '</pubDate>
            <category>' . $data['category'] . '</category>
            <author>' . $data['author'] . '</author>
        </item>
        ';

        $this->assertXmlStringEqualsXmlString($expect, $item->asXML()->asXML());
    }

    public function testAsXMLWithRelated()
    {
        $data = $this->dataForXmlTests();

        $item = new Item();
        $item
            ->author($data['author'])
            ->pubDate($data['now'])
            ->title($data['title'])
            ->link($data['link'])
            ->turboContent($data['turboContent'])
            ->category($data['category']);

        $relatedItemsList = new RelatedItemsList();
        $item->addRelatedItemsList($relatedItemsList);

        $expect = '
        <item turbo="true">
            <title>' . $data['title'] . '</title>
            <link>' . $data['link'] . '</link>
            <turbo:content xmlns:turbo="http://turbo.yandex.ru"><![CDATA[' . $data['turboContent'] . ']]></turbo:content>
            <pubDate>' . $data['pubDate'] . '</pubDate>
            <category>' . $data['category'] . '</category>
            <author>' . $data['author'] . '</author>
            <yandex:related/>
        </item>
        ';

        $this->assertXmlStringEqualsXmlString($expect, $item->asXML()->asXML());
    }

    private function dataForXmlTests()
    {
        $now = time();

        $data = [
            'now'          => $now,
            'pubDate'      => date(DATE_RSS, $now),
            'title'        => 'Thirst page!',
            'link'         => 'http://www.example.com/page1.html',
            'turboContent' => 'Some content here!<br>Second content string.',
            'author'       => 'John Smith',
            'category'     => 'Auto',
        ];

        return $data;
    }
}
