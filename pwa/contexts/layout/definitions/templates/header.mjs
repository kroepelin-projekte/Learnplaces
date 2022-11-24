export default `
    <template id='header-template'>
        <div class='Header' style='height: 60px;'>
            <div class='Header-item position-absolute right-0'>
                <slot name='menu'></slot>
            </div>
        </div>
        
    </template>
`;