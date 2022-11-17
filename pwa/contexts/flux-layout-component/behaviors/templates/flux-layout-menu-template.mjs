export default `
    <template id='flux-layout-menu-template'>
    <details class='details-reset details-overlay' open>
    <summary class='btn' aria-haspopup='true'>
    <slot name='flux-layout-menu-title'></slot>
    </summary><div class='SelectMenu right-0'>
    <div class='SelectMenu-modal'>
    <div class='SelectMenu-list'>
    <slot name='flux-layout-menu-item'></slot>
    </div>
    </div>
    </div>
    </details>
    </template>
`;