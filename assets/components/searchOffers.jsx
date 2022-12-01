
import {render, unmountComponentAtNode} from "react-dom";
import React, {useCallback, useRef, useState} from "react";
import AsyncSelect from "react-select/async";

function SearchForm () {
    const keywordField = useRef(' ')
    const [selectedOption, setSelectedOption] = useState(null)
    const [keywordValue, setKeywordValue] = useState(null)
    const [citySelect, setCitySelect] = useState(null)
    const parameters = new URLSearchParams;
    const getUrl = new URL(window.location.href);

    const onSubmit = useCallback( (e) => {
        e.preventDefault()
        setKeywordValue(keywordField.current.value)
        setCitySelect(selectedOption.value)

        if(keywordField.current.value){
            parameters.set('keyword', keywordField.current.value)
            parameters.set('city', selectedOption.value)
        } else {
            parameters.set('keyword', ' ')
            parameters.set('city', selectedOption.value)
        }

        fetch(getUrl.pathname + "?" + parameters.toString() ,{
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        }).then(response => response.json()).then(data =>{
            const content = document.getElementById('content');
            content.innerHTML = data.content;
        })
            .catch(error => alert(error))

    })

    function handleOptionChange(selectedOption) {
        setSelectedOption(selectedOption)
        console.log( `Selected: ${selectedOption.value}`)
    }

    function mapOptionsToValues(options) {
        return options.map(option => ({
            value: option.label,
            label: option.name + ' (' + option.departmentNumber +')'
        }));
    }

    function getOptions(inputValue, callback) {
        if (!inputValue) {
            return callback([]);
        }
        const fetchURL = `api/cities?label=${inputValue}`;
        fetch(fetchURL).then(response => {
            response.json().then(data => {
                const results = data['hydra:member'];
                const filteredOptions = results.filter(city => city.label.toLowerCase().includes(inputValue.toLowerCase()))
                callback(mapOptionsToValues(filteredOptions));
            })
        })
    }


    return <div>
        <form onSubmit={onSubmit} method="POST" >
            <div className="form-group">
                <label htmlFor="keyword">Quoi</label>
                <input
                    type="text"
                    ref={keywordField}
                    name="keyword"
                    className="form-control"
                    id="keyword"
                    placeholder="Mots-clés"/>
            </div>
            <div className="form-group">
                <label htmlFor="city-select">Où</label>
                <AsyncSelect
                    placeholder="Paris"
                    id="city-select"
                    cacheOptions
                    value={selectedOption}
                    loadOptions={getOptions}
                    onChange={handleOptionChange}
                    defaultOptions={false}
                    required
                />
            </div>
            <button type="submit" >Rechercher</button>
        </form>
    </div>
}



class SearchOffers extends HTMLElement{

    connectedCallback () {

        render(<SearchForm/>, this)
    }
    disconnectedCallback(){
        unmountComponentAtNode(this)
    }
}


customElements.define('search-offers', SearchOffers)