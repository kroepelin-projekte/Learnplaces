export default `
    <template id='content-container-template'>
        <div class="container-lg clearfix p-5">
    <div class="pb-5">  <h1><slot name='title' template-id='string-template' slot-value-type="key-value-item"></slot></h1>
        <p><slot name='description' template-id='string-template' slot-value-type="key-value-item"></slot></p>
    </div>

    <div class="Box">
        <div class="Box-header d-flex flex-items-center">
            <h3 class="Box-title overflow-hidden flex-auto">
                Overview
            </h3>
            <slot name='menu'></slot>
        </div>
        <slot name='content'></slot>
    </div>
</div>
</template>
`;