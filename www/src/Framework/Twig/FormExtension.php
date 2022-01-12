<?php

namespace Framework\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FormExtension extends AbstractExtension
{

    public function getFunctions(): array
    {
        return [
            new TwigFunction('field', [$this, 'field'], [
                'is_safe' => ['html'],
                'needs_context' => true
            ])
        ];
    }

    public function field($context, string $key, $value, string $label, array $options = []): string
    {
        var_dump($context);
        $type = $options['type'] ?? 'text';
        if ($type === 'textarea') {
            $input = $this->textarea($key, $value);
        } else {
            $input = $this->input($key, $value);
        }
        return "
        <div class=\"form-group\">
            <label for=\"name\">{$label}</label>
            {$input}
        </div>
        ";
    }

    private function input(string $key, ?string $value): string
    {
        return "<input 
                type=\"text\"
                class=\"form-control\"
                name=\"{$key}\"
                id=\"{$key}\"
                value=\"{$value}\"
            >";
    }

    private function textarea(string $key, ?string $value): string
    {
        return "<textarea class=\"form-control\" name=\"{$key}\" id=\"{$key}\">{$value}</textarea>";
    }
}
