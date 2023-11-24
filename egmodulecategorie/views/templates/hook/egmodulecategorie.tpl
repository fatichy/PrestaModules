{* {foreach from=$categories item=categorie }
    <p> {$categorie.title} </p>
    <p> {$categorie.image} </p>
    <p> {$categorie.title} </p>
{/foreach} *}

{if isset($categories) && !empty($categories) && $status == 1}
    <div id="" class="container">
        <div class="row">
            <div class="block-title">
                <h2 class="title-light">
                    {l s='Title' d='Modules.categorie.categorie'}
                </h2>
                <p class="subtitle-bold">
                    {l s='Sub title' d='Modules.categorie.categorie'}
                </p>
            </div>
            <div class="col-md-12 pd0">
                {foreach from=$categories item=categorie}
                <div class="col-md-4">
                    <div class="">
                        {if isset($categorie.image) && !empty($categorie.image)}
                            <img
                                class="replace-2x img-responsive"
                                src="{$url}{$categorie.image|escape:'html':'UTF-8'}"
                                width="100%;" />
                        {/if}
                    </div>
                </div>
            {/foreach}
            </div>
        </div>
    </div>
{/if}
