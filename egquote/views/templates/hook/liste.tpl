


<div class="btn-group">
    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton8" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false">
        <i class="material-icons">shopping_basket</i> Quotation <span id="quote-count"></span>
    </button>
    <div class="dropdown-menu" id="" aria-labelledby="dropdownMenuButton8">

    <div id="quotationsList"> 
        {foreach from=$quotations item=quotation}

            {if isset($quotation) && !empty($quotation)}
                <div>{$quotation->name[1]}</div>
            {/if}
        {/foreach}
        </div>

      
        <a href={$link} class="btn btn-success" role="button">Quotations</a>
  
    </div>
</div>