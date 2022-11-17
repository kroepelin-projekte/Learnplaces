export default `
    <template id='flux-layout-content-container-template'>
        <div class="container-lg clearfix p-5">
    <div class="pb-5">  <h1>Kurstitel (Todo)</h1>
        <p>Eine kurze Beschreibung des Kurses (Todo)</p>
        </div>

    <div class="Box">

        <div class="Box-header d-flex flex-items-center">
            <h3 class="Box-title overflow-hidden flex-auto">
                Overview
            </h3>


            <details class="dropdown details-reset details-overlay d-inline-block">
                <summary class="color-fg-muted p-2 d-inline" aria-haspopup="true">
                    Learnplaces
                    <div class="dropdown-caret"></div>
                </summary>

                <ul class="dropdown-menu dropdown-menu-se">
                    <li><a class="dropdown-item" href="#">Overview</a></li>
                    <li><a class="dropdown-item" href="#">Learnplace 1 Todo</a></li>
                </ul>
            </details>
        </div>
        <div class="Box-body">
            <button class="btn btn-primary" type="button" id="btnGetLoc">Locate my Position (ToDo)</button>
        </div>
        
         <slot name='flux-layout-content-container-content'></slot>


    </div>
</div>

    </template>
`;