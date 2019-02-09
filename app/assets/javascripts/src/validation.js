class Validation
{
  constructor(error, validation)
  {
    this.error = error;
    this.validation = validation;
  }

  test(field)
  {
    if (typeof this.validation === 'function')
    {
      return this.validation(field);
    }
    if (!this.validation.test(field.val()))
    {
      return false;
    }
    return true;
  }
}

export default Validation;
