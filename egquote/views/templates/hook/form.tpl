{extends file=$layout}
{block name='content'}

    <div class="container mt-5">
        <form>
            <!-- Email Input -->
            <section id="content" class="page-content card card-block">
                <div class="form-group">
                    <label for="inputEmail">Email:</label>
                    <input type="email" class="form-control" id="inputEmail" placeholder="Enter your email" required>
                </div>

                <!-- Select with Yes/No Options -->
                <div class="form-group">
                    <label for="selectOption">Are you a whole-seller?</label>
                    <select class="form-control" id="selectOption">
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </select>
                </div>

                <!-- Text Input -->
                <div class="form-group">
                    <label for="inputText">Your annual budget:</label>
                    <input type="text" class="form-control" id="inputText" placeholder="Enter text">
                </div>

                <!-- Textarea Input -->
                <div class="form-group">
                    <label for="textareaInput">Any special note:</label>
                    <textarea class="form-control" id="textareaInput" rows="3" placeholder="Enter text"></textarea>
                </div>

                <!-- File Input -->
                <div class="form-group">
                    <label for="fileInput">Attach file:</label>
                    <input type="file" class="form-control-file" id="fileInput">
                </div>

                <!-- Two Buttons Next to Each Other on the Right -->
                <div class="form-group d-flex justify-content-end">
                    <button type="button" class="btn btn-primary ml-2">Submit</button>
                    <a href='' class="btn btn-success" role="button">Update Quote</a>
                    
        </form>
{foreach from=$quotations item=quotation}
    <p> {$quotation->name[1]} - {$quotation->price}</p>
    
{/foreach}
    <p class="text-dark "   >Total :    {$total}</p>
        </section>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


{/block}