<?php

namespace Domain\Users\Enums;

enum SexEnum : string
{
    case MASCULINE = 'MASCULINE';
    case FEMININE = 'FEMININE';
    case NOT_APPLICABLE = 'NOT_APPLICABLE';
}
