export default `
    <template id='menu-template'>
    <details class='details-reset details-overlay' open>
    <summary class='btn' aria-haspopup='true'>
    <slot name='title' template-id='string-template'  slot-value-type="key-value-item" ></slot>
    </summary><div class='SelectMenu'>
    <div class='SelectMenu-modal'>
    <div class='SelectMenu-list'>
    <slot name='items' template-id='menu-item-template'  slot-value-type="key-value-list" add-on-click-event='true'></slot>
    </div>
    </div>
    </div>
    </details>
    </template>
`;