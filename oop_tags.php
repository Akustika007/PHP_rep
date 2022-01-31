<?php

declare(strict_types=1);

abstract class Tag {
    private string $name;

    private array $attrs = [];

    public function __construct(string $name) {
        $this->name = $name;
    }

    public function attr($atrName, $atrArg): Tag {
        $this->attrs[$atrName] = $atrArg;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    protected function getFormattedAttrs(): string
    {
        $formattedAttrs = '';

        foreach ($this->attrs as $attr => $arg) {
            $formattedAttrs .= "{$attr} = \"{$arg}\" ";
        }

        return $formattedAttrs;
    }
}

final class SingleTag extends Tag {
    public function render(): string
    {
        return "<{$this->getName()} {$this->getFormattedAttrs()}/>";
    }
}

final class PairTag extends Tag {
    /**
     * @var $childTags Tag[]
     */
    private array $childTags = [];

    public function appendChild(Tag $tag): PairTag
    {
        $this->childTags[] = $tag;

        return $this;
    }

    public function render(): string
    {
        $name = $this->getName();

        return <<<HTML
        <$name {$this->getFormattedAttrs()}>
            {$this->getFormattedChildTags()}
        </$name>
        HTML;
    }

    private function getFormattedChildTags(): string
    {
        $formattedChildTags = '';

        foreach ($this->childTags as $childTag) {
            $formattedChildTags .= $childTag->render();
        }

        return $formattedChildTags;
    }
}

//<form>
//	<label>
//		<img src="f1.jpg" alt="f1 not found">
//		<input type="text" name="f1">
//	</label>
//	<label>
//		<img src="f2.jpg" alt="f2 not found">
//		<input type="password" name="f2">
//	</label>
//	<input type="submit" value="Send">
//</form>

$img1 = new SingleTag('img');
$img1->attr('src', 'f1.jpg')
    ->attr('alt', 'f1 not found');

$input1 = new SingleTag('input');
$input1->attr('type', 'text')
    ->attr('name', 'f1');

$label1 = new PairTag('label');
$label1->appendChild($img1)
    ->appendChild($input1);


$img2 = new SingleTag('img');
$img2->attr('src', 'f2.jpg')
    ->attr('alt', 'f2 not found');

$input2 = new SingleTag('input');
$input2->attr('type', 'password')
    ->attr('name', 'f2');

$label2 = new PairTag('label');
$label2->appendChild($img2)
    ->appendChild($input2);

$input3 = new SingleTag('input');
$input3->attr('type', 'submit')
    ->attr('value', 'Send');

$form = new PairTag('form');
$form->appendChild($label1)
    ->appendChild($label2)
    ->appendChild($input3);

echo $form->render();