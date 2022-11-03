/**
 * @param {ReactorPayload} reactorPayload
 */
createReactor(reactorPayload) {
  reactorPayload.bindingTypes.forEach(bindingType => {
    bindingType.connect(
      {
        address: reactorPayload.channel.payload.channel
        reactorId: reactorPayload.reactorId,

      }
    )
  })
}