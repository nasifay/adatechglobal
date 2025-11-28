<?php
// Reusable admin form partial.
// Expects these variables to be set by the including page:
// - $form_action (string) - form action URL (default current page)
// - $form_method (string) - 'post' or 'get' (default 'post')
// - $form_enctype (string) - e.g. 'multipart/form-data' if needed
// - $hidden (array) - associative array of hidden inputs
// - $fields (array) - array of field definitions
// - $submit_label (string) - submit button label

if (!isset($form_action)) $form_action = '';
if (!isset($form_method)) $form_method = 'post';
if (!isset($form_enctype)) $form_enctype = '';
if (!isset($hidden)) $hidden = [];
if (!isset($fields)) $fields = [];
if (!isset($submit_label)) $submit_label = 'Save';

if (!function_exists('esc')) {
    require_once __DIR__ . '/../../includes/helpers.php';
}

?>
<form action="<?php echo $form_action ?: htmlspecialchars($_SERVER['SCRIPT_NAME']); ?>" method="<?php echo $form_method; ?>" <?php echo $form_enctype ? 'enctype="' . $form_enctype . '"' : ''; ?>>
    <?php foreach ($hidden as $k => $v): ?>
        <input type="hidden" name="<?php echo esc($k); ?>" value="<?php echo esc($v); ?>">
    <?php endforeach; ?>

    <?php foreach ($fields as $f):
        $type = $f['type'] ?? 'text';
        $name = $f['name'] ?? '';
        $label = $f['label'] ?? '';
        $value = $f['value'] ?? ($f['default'] ?? '');
        $required = !empty($f['required']) ? 'required' : '';
        $attrs = '';
        if (!empty($f['attrs']) && is_array($f['attrs'])) {
            foreach ($f['attrs'] as $ak => $av) $attrs .= ' ' . $ak . '="' . esc($av) . '"';
        }
    ?>
        <div class="mb-2">
            <?php if ($label): ?><label><?php echo esc($label); ?><br><?php endif; ?>
            <?php if ($type === 'text' || $type === 'email' || $type === 'number' || $type === 'hidden'): ?>
                <input class="form-control" type="<?php echo $type === 'hidden' ? 'hidden' : $type; ?>" name="<?php echo esc($name); ?>" value="<?php echo esc($value); ?>" <?php echo $required . ' ' . $attrs; ?>>
            <?php elseif ($type === 'textarea'): ?>
                <textarea class="form-control" name="<?php echo esc($name); ?>" rows="<?php echo intval($f['rows'] ?? 4); ?>" <?php echo $required . ' ' . $attrs; ?>><?php echo esc($value); ?></textarea>
            <?php elseif ($type === 'select'): ?>
                <select class="form-control" name="<?php echo esc($name); ?>" <?php echo $required . ' ' . $attrs; ?>>
                    <?php if (!empty($f['options']) && is_array($f['options'])): foreach ($f['options'] as $optVal => $optLabel): ?>
                        <option value="<?php echo esc($optVal); ?>" <?php echo ((string)$optVal === (string)$value) ? 'selected' : ''; ?>><?php echo esc($optLabel); ?></option>
                    <?php endforeach; endif; ?>
                </select>
            <?php elseif ($type === 'picker'): ?>
                <?php
                    // Picker renders a select of existing items plus a Pick button and preview area.
                    // Options can be an associative array value=>label OR an array of arrays with keys 'value','label','path'.
                    $selectId = $name;
                ?>
                <div class="d-flex" style="gap:8px;align-items:center">
                    <select id="<?php echo esc($selectId); ?>" class="form-control" name="<?php echo esc($name); ?>" <?php echo $required . ' ' . $attrs; ?>>
                        <?php if (!empty($f['options']) && is_array($f['options'])):
                            foreach ($f['options'] as $optKey => $optVal):
                                if (is_array($optVal)) {
                                    $optValue = $optVal['value'] ?? ($optVal[0] ?? '');
                                    $optLabel = $optVal['label'] ?? ($optVal[1] ?? $optValue);
                                    $optPath = $optVal['path'] ?? ($optVal['path'] ?? '');
                                } else {
                                    $optValue = $optKey;
                                    $optLabel = $optVal;
                                    $optPath = '';
                                }
                        ?>
                            <option value="<?php echo esc($optValue); ?>" <?php echo ((string)$optValue === (string)$value) ? 'selected' : ''; ?> <?php echo $optPath ? 'data-path="' . esc($optPath) . '"' : ''; ?>><?php echo esc($optLabel); ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="openImagePicker('<?php echo esc($selectId); ?>', '<?php echo esc($selectId); ?>-preview')">Pick</button>
                    <div id="<?php echo esc($selectId); ?>-preview" class="picker-preview" style="margin-left:8px"></div>
                </div>
            <?php elseif ($type === 'checkbox'): ?>
                <label><input type="checkbox" name="<?php echo esc($name); ?>" value="1" <?php echo (!empty($value) ? 'checked' : ''); ?>> <?php echo esc($f['help'] ?? ''); ?></label>
            <?php elseif ($type === 'file'): ?>
                <input class="form-control" type="file" name="<?php echo esc($name); ?>" <?php echo $attrs; ?>>
            <?php elseif ($type === 'html'): ?>
                <?php echo $f['html'] ?? ''; ?>
            <?php endif; ?>
            <?php if ($label): ?></label><?php endif; ?>
            <?php if (!empty($f['note'])): ?><div class="small text-muted"><?php echo esc($f['note']); ?></div><?php endif; ?>
        </div>
    <?php endforeach; ?>

    <div style="margin-top:8px"><button class="btn btn-success"><?php echo esc($submit_label); ?></button></div>
</form>
