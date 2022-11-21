export default `
    <template id='map-template'>
    <flux-map id="flux-map">
        <slot name="marker" template-id='map-marker-template'  slot-value-type="object-list"></slot>
    </flux-map>
</div>
</template>
`;