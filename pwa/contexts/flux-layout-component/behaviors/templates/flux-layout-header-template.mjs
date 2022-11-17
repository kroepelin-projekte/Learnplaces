export default `
    <template id='flux-layout-header-template'>
        <div class='Header' style='height: 60px;'>
            <div class='Header-item position-absolute right-0'>
                <slot name='flux-layout-header-menu'></slot>
            </div>
        </div>
    </template>
`;