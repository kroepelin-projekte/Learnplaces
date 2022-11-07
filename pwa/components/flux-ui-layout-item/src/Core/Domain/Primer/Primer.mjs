const PrimerCssResponse = await fetch('./assets/primer.css')
export default await PrimerCssResponse.text();