import React, {useEffect} from "react";
import {usePaginatedFetch} from "../hooks/hooks";
import {render, unmountComponentAtNode} from "react-dom";

const Offers = React.memo(({keyword, city}) =>{
    const {items: offers, load, loading, count, hasMore} = usePaginatedFetch('api/offers?slug=' + keyword + '&City.label=' + city)

    useEffect(() => {load()}, [])

    return <div>
        {loading && 'chargement ...'}
        <Title count={count}/>
        {offers.map(offer => <Offer key={offer.id} offer={offer} className="offers-list"/>) }
        {hasMore && <button disabled={loading} onClick={load}>Charger plus d'offres</button>}
    </div>
})

function Title({count}){

    return <div> {count >=1 ? <h3>{count} offre{count > 1 ? 's' : '' }</h3> : <h3>Aucune offre disponible</h3>}
    </div>
}

const Offer = React.memo( ({offer}) => {
    const date = new Date(offer.published_at)
    return <div>
        <h3>{offer.title} H/F</h3>
        <p><strong>{offer.recruiter.name}</strong></p>
        <p>{offer.description}</p>
        <p>Salaire : {offer.salary}</p>
        <p>Type de contrat : {offer.contrat_type}</p>
        <p>Nombre de postes à pourvoir : {offer.positions}</p>
        <p>Lieu : {offer.City.name}</p>
        <p>Date de publication : {date.toLocaleString(undefined,)}</p>
    </div>
})


class OffersList extends HTMLElement{

    connectedCallback () {

        const keywords = this.dataset.keyword
        const cityLabel = this.dataset.city

        render(<Offers keyword={keywords} city={cityLabel}/>, this)
    }
    disconnectedCallback(){
        unmountComponentAtNode(this)
    }
}

customElements.define('offers-list', OffersList)