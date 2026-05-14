<?php

function text_field(string $label, string $name, mixed $value, array $options = []): string {
    $id          = $options['id'] ?? rtrim(preg_replace('/[^\w]+/', '_', $name), '_');
    $type        = $options['type'] ?? 'text';
    $textarea    = $options['textarea'] ?? false;
    $required    = $options['required'] ?? false;
    $rows        = $options['rows'] ?? 3;
    $placeholder = isset($options['placeholder']) ? ' placeholder="' . htmlspecialchars($options['placeholder']) . '"' : '';
    $required_attr = $required ? ' required' : '';

    if ($textarea) {
        return '<div>'
            . '<label for="' . $id . '">' . htmlspecialchars($label) . '</label>'
            . '<textarea id="' . $id . '" name="' . $name . '" rows="' . $rows . '"' . $required_attr . '>' . htmlspecialchars((string) $value) . '</textarea>'
            . '</div>';
    }

    return '<div>'
        . '<label for="' . $id . '">' . htmlspecialchars($label) . '</label>'
        . '<input type="' . $type . '" id="' . $id . '" name="' . $name . '" value="' . htmlspecialchars((string) $value) . '"' . $placeholder . $required_attr . '>'
        . '</div>';
}
